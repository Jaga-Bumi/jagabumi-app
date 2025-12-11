<?php

namespace App\Http\Controllers;

use App\Http\Requests\Quest\CreateQuestRequest;
use App\Http\Requests\Quest\AddWinnersRequest;
use App\Http\Requests\Quest\UpdateQuestRequest;
use App\Models\Organization;
use App\Models\OrganizationMember;
use App\Models\Prize;
use App\Models\Quest;
use App\Models\QuestParticipant;
use App\Models\QuestWinner;
use App\Services\BlockchainService;
use App\Services\FilebaseService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class QuestController extends Controller
{
    // Read all quests
    public function getAll()
    {
        $query = Quest::query();
  
        // Filter by status
        if (request('status')) {
            $query->where('status', request('status'));
        }
        // No else - show all statuses by default
        
        // Search by title or description
        if (request('search')) {
            $query->where(function($q) {
            $q->where('title', 'like', '%' . request('search') . '%')
                ->orWhere('desc', 'like', '%' . request('search') . '%');
            });
        }
        
        // Sort
        $sort = request('sort', 'newest');
        if ($sort === 'newest') {
            $query->latest();
        } elseif ($sort === 'ending_soon') {
            $query->orderBy('quest_end_at', 'asc');
        }
        
        $quests = $query->paginate(6);
        
        return view('pages.quests.index', compact('quests'));

    }

    public function getDetail($slug)
    {
        $quest = Quest::where('slug', $slug)
            ->with([
                'organization:id,name,handle,logo_img,org_email,website_url,instagram_url,x_url,facebook_url',
                'organization.organizationMembers' => function($query) {
                    $query->where('status', 'ACTIVE');
                },
                'organization.organizationMembers.user:id,name,email,avatar_url',
                'prizes',
            ])
            ->withCount('questParticipants')
            ->firstOrFail();

        $userParticipation = null;
        if (Auth::check()) {
            $userParticipation = QuestParticipant::where('quest_id', $quest->id)
                ->where('user_id', Auth::id())
                ->first();
        }

        // Get submissions (COMPLETED, APPROVED, REJECTED)
        $submissions = QuestParticipant::where('quest_id', $quest->id)
            ->whereIn('status', ['COMPLETED', 'APPROVED', 'REJECTED'])
            ->with('user:id,name,avatar_url,wallet_address')
            ->orderBy('submission_date', 'desc')
            ->get();

        return view('pages.quests.show', compact('quest', 'userParticipation', 'submissions'));
    }

    // Create quest
    public function create(CreateQuestRequest $request)
    {
        try {
            // Organization validation is handled by IsOrgManager middleware
            $organization = $request->_organization ?? Organization::findOrFail($request->org_id);

            DB::beginTransaction();

            // Generate unique slug with random string
            $slug = Str::slug($request->title) . '-' . Str::random(6);

            // Handle banner upload
            $bannerName = null;
            if ($request->hasFile('banner')) {
                $bannerFile = $request->file('banner');
                $bannerName = Str::uuid() . '_' . str_replace(' ', '_', $bannerFile->getClientOriginalName());
                $uploadPath = public_path('QuestStorage/Banner');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }
                $bannerFile->move($uploadPath, $bannerName);
                Log::info("Quest banner uploaded: " . $bannerName);
            }

            // Create quest
            $quest = Quest::create([
                'title' => $request->title,
                'slug' => $slug,
                'desc' => $request->desc,
                'banner_url' => $bannerName,
                'location_name' => $request->location_name,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'radius_meter' => $request->radius_meter ?? 100,
                'liveness_code' => $request->liveness_code,
                'registration_start_at' => $request->registration_start_at,
                'registration_end_at' => $request->registration_end_at,
                'quest_start_at' => $request->quest_start_at,
                'quest_end_at' => $request->quest_end_at,
                'judging_start_at' => $request->judging_start_at,
                'judging_end_at' => $request->judging_end_at,
                'prize_distribution_date' => $request->prize_distribution_date,
                'participant_limit' => $request->participant_limit,
                'winner_limit' => $request->winner_limit,
                'org_id' => $organization->id,
                'status' => 'IN REVIEW', // Default status, admin needs to approve
            ]);

            // Handle certificate prize image upload
            $certImageName = null;
            if ($request->hasFile('cert_image')) {
                $certFile = $request->file('cert_image');
                $certImageName = Str::uuid() . '_cert_' . str_replace(' ', '_', $certFile->getClientOriginalName());
                $uploadPath = public_path('PrizeStorage');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }
                $certFile->move($uploadPath, $certImageName);
                Log::info("Certificate image uploaded: " . $certImageName);
            }

            // Create certificate prize
            $certificatePrize = Prize::create([
                'name' => $request->cert_name,
                'type' => 'CERTIFICATE',
                'description' => $request->cert_description,
                'image_url' => $certImageName,
                'quest_id' => $quest->id,
            ]);

            // Create coupon prize if provided
            $couponPrize = null;
            if ($request->coupon_name) {
                $couponImageName = null;
                if ($request->hasFile('coupon_image')) {
                    $couponFile = $request->file('coupon_image');
                    $couponImageName = Str::uuid() . '_coupon_' . str_replace(' ', '_', $couponFile->getClientOriginalName());
                    $uploadPath = public_path('PrizeStorage');
                    if (!file_exists($uploadPath)) {
                        mkdir($uploadPath, 0777, true);
                    }
                    $couponFile->move($uploadPath, $couponImageName);
                    Log::info("Coupon image uploaded: " . $couponImageName);
                }

                $couponPrize = Prize::create([
                    'name' => $request->coupon_name,
                    'type' => 'COUPON',
                    'description' => $request->coupon_description,
                    'image_url' => $couponImageName,
                    'quest_id' => $quest->id,
                ]);
            }

            DB::commit();

            $prizes = $couponPrize ? [$certificatePrize, $couponPrize] : [$certificatePrize];

            // Check if request expects JSON (API) or redirect (web form)
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Quest created successfully. Waiting for admin approval.',
                    'redirect' => route('organization.quests.index'),
                    'data' => [
                        'quest' => [
                            'id' => $quest->id,
                            'title' => $quest->title,
                            'slug' => $quest->slug,
                            'status' => $quest->status,
                            'organization' => [
                                'id' => $organization->id,
                                'name' => $organization->name,
                            ],
                            'prizes' => collect($prizes)->map(fn($prize) => [
                                'id' => $prize->id,
                                'name' => $prize->name,
                                'type' => $prize->type,
                            ]),
                            'created_at' => $quest->created_at,
                        ],
                    ],
                ], 201);
            }

            return redirect()->route('organization.quests.index')
                ->with('success', 'Quest created successfully! It is now IN REVIEW and waiting for admin approval.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Quest creation failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create quest. Please try again.',
                    'error' => config('app.debug') ? $e->getMessage() : null
                ], 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create quest: ' . $e->getMessage());
        }
    }

    // Delete quest
    public function destroy($id)
    {
        try {
            $quest = Quest::with('prizes')->findOrFail($id);
            
            if ($quest->status !== 'IN REVIEW') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only quests with IN REVIEW status can be deleted.'
                ], 403);
            }

            DB::beginTransaction();

            // Delete images
            if ($quest->banner_url) {
                $bannerPath = public_path('QuestStorage/Banner/' . $quest->banner_url);
                if (file_exists($bannerPath)) {
                    unlink($bannerPath);
                }
            }

            foreach ($quest->prizes as $prize) {
                if ($prize->image_url) {
                    $prizePath = public_path('PrizeStorage/' . $prize->image_url);
                    if (file_exists($prizePath)) {
                        unlink($prizePath);
                    }
                }
            }

            $quest->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Quest and all associated images deleted successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete quest. Please try again.',
            ], 500);
        }
    }
    
    // Update quest
    public function update($id, UpdateQuestRequest $request)
    {
        try {
            $quest = Quest::with('prizes')->findOrFail($id);

            if ($quest->status !== 'IN REVIEW') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only quests with IN REVIEW status can be updated.'
                ], 403);
            }

            $organization = $request->_organization ?? Organization::findOrFail($quest->org_id);
            DB::beginTransaction();

            // Update slug if title changed
            if ($request->title !== $quest->title) {
                $quest->slug = Str::slug($request->title) . '-' . Str::random(6);
            }

            // Handle banner upload
            if ($request->hasFile('banner')) {
                // Delete old banner if exists
                if ($quest->banner_url) {
                    $oldBannerPath = public_path('QuestStorage/Banner/' . $quest->banner_url);
                    if (file_exists($oldBannerPath)) {
                        unlink($oldBannerPath);
                    }
                }
                
                $bannerFile = $request->file('banner');
                $bannerName = Str::uuid() . '_' . str_replace(' ', '_', $bannerFile->getClientOriginalName());
                $uploadPath = public_path('QuestStorage/Banner');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }
                $bannerFile->move($uploadPath, $bannerName);
                Log::info("Quest banner updated: " . $bannerName);
                $quest->banner_url = $bannerName;
            }

            // Update quest fields
            $quest->update([
                'title' => $request->title,
                'desc' => $request->desc,
                'location_name' => $request->location_name,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'radius_meter' => $request->radius_meter,
                'liveness_code' => $request->liveness_code,
                'registration_start_at' => $request->registration_start_at,
                'registration_end_at' => $request->registration_end_at,
                'quest_start_at' => $request->quest_start_at,
                'quest_end_at' => $request->quest_end_at,
                'judging_start_at' => $request->judging_start_at,
                'judging_end_at' => $request->judging_end_at,
                'prize_distribution_date' => $request->prize_distribution_date,
                'participant_limit' => $request->participant_limit,
                'winner_limit' => $request->winner_limit,
            ]);

            // Update certificate prize
            $certificatePrize = $quest->prizes()->where('type', 'CERTIFICATE')->first();
            $certImageName = $certificatePrize->image_url;
            
            if ($request->hasFile('cert_image')) {
                // Delete old certificate image if exists
                if ($certImageName) {
                    $oldCertPath = public_path('PrizeStorage/' . $certImageName);
                    if (file_exists($oldCertPath)) {
                        unlink($oldCertPath);
                    }
                }
                
                $certFile = $request->file('cert_image');
                $certImageName = Str::uuid() . '_cert_' . str_replace(' ', '_', $certFile->getClientOriginalName());
                $uploadPath = public_path('PrizeStorage');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }
                $certFile->move($uploadPath, $certImageName);
                Log::info("Certificate image updated: " . $certImageName);
            }

            $certificatePrize->update([
                'name' => $request->cert_name,
                'description' => $request->cert_description,
                'image_url' => $certImageName,
            ]);

            // Update or create coupon prize
            if ($request->coupon_name) {
                $couponPrize = $quest->prizes()->where('type', 'COUPON')->first();
                $couponImageName = $couponPrize->image_url ?? null;
                
                if ($request->hasFile('coupon_image')) {
                    // Delete old coupon image if exists
                    if ($couponImageName) {
                        $oldCouponPath = public_path('PrizeStorage/' . $couponImageName);
                        if (file_exists($oldCouponPath)) {
                            unlink($oldCouponPath);
                        }
                    }
                    
                    $couponFile = $request->file('coupon_image');
                    $couponImageName = Str::uuid() . '_coupon_' . str_replace(' ', '_', $couponFile->getClientOriginalName());
                    $uploadPath = public_path('PrizeStorage');
                    if (!file_exists($uploadPath)) {
                        mkdir($uploadPath, 0777, true);
                    }
                    $couponFile->move($uploadPath, $couponImageName);
                    Log::info("Coupon image updated: " . $couponImageName);
                }

                if ($couponPrize) {
                    $couponPrize->update([
                        'name' => $request->coupon_name,
                        'description' => $request->coupon_description,
                        'image_url' => $couponImageName,
                    ]);
                } else {
                    Prize::create([
                        'name' => $request->coupon_name,
                        'type' => 'COUPON',
                        'description' => $request->coupon_description,
                        'image_url' => $couponImageName,
                        'quest_id' => $quest->id,
                    ]);
                }
            } else {
                $quest->prizes()->where('type', 'COUPON')->delete();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Quest updated successfully.',
                'data' => [
                    'quest' => [
                        'id' => $quest->id,
                        'title' => $quest->title,
                        'slug' => $quest->slug,
                        'status' => $quest->status,
                        'organization' => [
                            'id' => $organization->id,
                            'name' => $organization->name,
                        ],
                        'prizes' => $quest->prizes()->get()->map(fn($prize) => [
                            'id' => $prize->id,
                            'name' => $prize->name,
                            'type' => $prize->type,
                        ]),
                        'updated_at' => $quest->updated_at,
                    ],
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update quest. Please try again.',
            ], 500);
        }
    }

    // Distribute Rewards to Winners
    public function distributeRewards($questId, BlockchainService $blockchain, FilebaseService $filebase)
    {
        DB::beginTransaction();
        try {
            $quest = Quest::with(['prizes', 'winners.user'])->findOrFail($questId);

            $user = Auth::user();
            if ($user->org_id !== $quest->org_id) {
                abort(403, 'Anda tidak memiliki akses untuk mendistribusikan reward quest ini.');
            }

            if ($quest->status !== 'ACTIVE' && $quest->status !== 'ENDED') {
                return response()->json([
                    'error' => 'Quest harus berstatus ACTIVE atau ENDED untuk distribusi reward.'
                ], 400);
            }

            $winners = $quest->winners()->where('reward_distributed', false)->get();
            
            if ($winners->isEmpty()) {
                return response()->json([
                    'error' => 'Tidak ada pemenang yang belum menerima reward.'
                ], 400);
            }

            $certificatePrize = $quest->prizes()->where('type', 'CERTIFICATE')->first();
            
            if (!$certificatePrize) {
                return response()->json([
                    'error' => 'Prize certificate tidak ditemukan untuk quest ini.'
                ], 400);
            }

            $recipients = [];
            $uris = [];

            foreach ($winners as $winner) {
                if (!$winner->user->wallet_address) {
                    continue;
                }

                // Create metadata for NFT
                $metadata = [
                    'name' => $certificatePrize->name,
                    'description' => $certificatePrize->description ?? "Sertifikat pemenang {$quest->title}",
                    'image' => "ipfs://{$certificatePrize->image_url}",
                    'attributes' => [
                        [
                            'trait_type' => 'Quest',
                            'value' => $quest->title
                        ],
                        [
                            'trait_type' => 'Winner',
                            'value' => $winner->user->name
                        ],
                        [
                            'trait_type' => 'Organization',
                            'value' => $quest->organization->name ?? 'JagaBumi'
                        ],
                        [
                            'trait_type' => 'Date',
                            'value' => now()->toDateString()
                        ]
                    ]
                ];

                // Upload metadata JSON to IPFS
                $metadataFileName = "metadata/quest_{$questId}_winner_{$winner->user_id}_" . time() . ".json";
                $metadataCid = $filebase->uploadToIpfs(
                    json_encode($metadata), 
                    $metadataFileName, 
                    'application/json'
                );

                if (!$metadataCid) {
                    throw new \Exception("Gagal upload metadata untuk winner {$winner->user->name}");
                }

                $recipients[] = $winner->user->wallet_address;
                $uris[] = "ipfs://{$metadataCid}";
            }

            if (empty($recipients)) {
                return response()->json([
                    'error' => 'Tidak ada pemenang dengan wallet address yang valid.'
                ], 400);
            }

            $txHash = $blockchain->mintBatch($recipients, $uris);

            if (!$txHash) {
                throw new \Exception("Gagal melakukan minting NFT, tidak ada transaction hash.");
            }

            foreach ($winners as $winner) {
                if ($winner->user->wallet_address) {
                    $winner->update([
                        'reward_distributed' => true,
                        'tx_hash' => $txHash,
                        'distributed_at' => now()
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Rewards berhasil didistribusikan ke ' . count($recipients) . ' pemenang.',
                'transaction_hash' => $txHash,
                'recipients_count' => count($recipients),
                'recipients' => $recipients
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'error' => 'Gagal mendistribusikan rewards: ' . $e->getMessage()
            ], 500);
        }
    }

    // Add Winners to Quest
    public function addWinners(AddWinnersRequest $request, $questId)
    {
        $quest = Quest::findOrFail($questId);

        // Validate authorization
        $user = Auth::user();
        if ($user->org_id !== $quest->org_id) {
            abort(403, 'Anda tidak memiliki akses untuk menambah pemenang quest ini.');
        }

        DB::beginTransaction();
        try {
            $addedWinners = [];

            foreach ($request->winners as $winnerData) {
                // Cek duplicate
                $exists = QuestWinner::where('quest_id', $questId)
                    ->where('user_id', $winnerData['user_id'])
                    ->exists();

                if ($exists) {
                    continue; // Skip if exists
                }

                $winner = QuestWinner::create([
                    'quest_id' => $questId,
                    'user_id' => $winnerData['user_id'],
                ]);

                $addedWinners[] = $winner->load('user');
            }

            DB::commit();

            return response()->json([
                'message' => count($addedWinners) . ' pemenang berhasil ditambahkan.',
                'winners' => $addedWinners
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Gagal menambahkan pemenang: ' . $e->getMessage()
            ], 500);
        }
    }

    // Get Winners of a Quest
    public function getWinners($questId)
    {
        $quest = Quest::findOrFail($questId);
        $winners = QuestWinner::where('quest_id', $questId)
            ->with('user:id,name,email,wallet_address,avatar_url')
            ->get();

        return response()->json([
            'quest' => $quest->only(['id', 'title', 'slug']),
            'winners' => $winners
        ]);
    }

    public function changeStatus($questId, $newStatus)
    {
        $quest = Quest::findOrFail($questId);

        $validStatuses = ['ACTIVE', 'ENDED', 'CANCELLED'];
        if (!in_array($newStatus, $validStatuses)) {
            return response()->json([
                'error' => 'Invalid status value.'
            ], 400);
        }

        $quest->status = $newStatus;
        $quest->save();

        return response()->json([
            'message' => 'Quest status updated successfully.',
            'quest' => $quest
        ]);
    }

    // Get organization quests
    public function organizationQuests()
    {
        $user = Auth::user();
        
        // Get user's organizations for the sidebar
        $userOrganizations = OrganizationMember::where('user_id', $user->id)
            ->where('status', 'ACTIVE')
            ->with('organization')
            ->get()
            ->map(fn($member) => [
                'id' => $member->organization->id,
                'name' => $member->organization->name,
                'handle' => $member->organization->handle,
                'logo_img' => $member->organization->logo_img,
                'role' => $member->role,
            ]);

        $firstOrg = $userOrganizations->first();
        $orgId = session('current_org_id', $firstOrg['id'] ?? null);
        $currentOrg = $userOrganizations->firstWhere('id', $orgId);
        
        if (!$currentOrg) {
            return redirect()->route('organization.dashboard')
                ->with('error', 'Please select or create an organization first.');
        }

        $quests = Quest::where('org_id', $orgId)
            ->with(['prizes', 'questParticipants'])
            ->withCount('questParticipants')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pages.organization.quests.index', compact('quests', 'userOrganizations', 'currentOrg'));
    }

    // Show quest creation form
    public function createView()
    {
        $user = Auth::user();
        
        // Get user's organizations for the sidebar
        $userOrganizations = OrganizationMember::where('user_id', $user->id)
            ->where('status', 'ACTIVE')
            ->with('organization')
            ->get()
            ->map(fn($member) => [
                'id' => $member->organization->id,
                'name' => $member->organization->name,
                'handle' => $member->organization->handle,
                'logo_img' => $member->organization->logo_img,
                'role' => $member->role,
            ]);

        $firstOrg = $userOrganizations->first();
        $orgId = session('current_org_id', $firstOrg['id'] ?? null);
        $currentOrg = $userOrganizations->firstWhere('id', $orgId);
        
        if (!$currentOrg) {
            return redirect()->route('organization.dashboard')
                ->with('error', 'Please select an organization first.');
        }

        $organization = Organization::findOrFail($orgId);

        return view('pages.organization.quests.create', compact('organization', 'userOrganizations', 'currentOrg'));
    }

    // Get organization prizes
    public function organizationPrizes()
    {
        $user = Auth::user();
        
        // Get user's organizations for the sidebar
        $userOrganizations = OrganizationMember::where('user_id', $user->id)
            ->where('status', 'ACTIVE')
            ->with('organization')
            ->get()
            ->map(fn($member) => [
                'id' => $member->organization->id,
                'name' => $member->organization->name,
                'handle' => $member->organization->handle,
                'logo_img' => $member->organization->logo_img,
                'role' => $member->role,
            ]);

        $firstOrg = $userOrganizations->first();
        $orgId = session('current_org_id', $firstOrg['id'] ?? null);
        $currentOrg = $userOrganizations->firstWhere('id', $orgId);
        
        if (!$currentOrg) {
            return redirect()->route('organization.dashboard')
                ->with('error', 'Please select or create an organization first.');
        }

        $quests = Quest::where('org_id', $orgId)
            ->where('status', 'ACTIVE')
            ->with(['prizes', 'winners.user'])
            ->withCount(['winners' => function($q) {
                $q->where('reward_distributed', false);
            }])
            ->get();

        return view('pages.organization.prizes', compact('quests', 'userOrganizations', 'currentOrg'));
    }

    // Show quest detail for organization admin
    public function showQuest($id)
    {
        $user = Auth::user();
        
        // Get user's organizations for the sidebar
        $userOrganizations = OrganizationMember::where('user_id', $user->id)
            ->where('status', 'ACTIVE')
            ->with('organization')
            ->get()
            ->map(fn($member) => [
                'id' => $member->organization->id,
                'name' => $member->organization->name,
                'handle' => $member->organization->handle,
                'logo_img' => $member->organization->logo_img,
                'role' => $member->role,
            ]);

        $firstOrg = $userOrganizations->first();
        $orgId = session('current_org_id', $firstOrg['id'] ?? null);
        $currentOrg = $userOrganizations->firstWhere('id', $orgId);
        
        if (!$currentOrg) {
            return redirect()->route('organization.dashboard')
                ->with('error', 'Please select an organization first.');
        }

        // Get quest with prizes and organization
        $quest = Quest::with(['prizes', 'organization', 'questParticipants'])
            ->withCount('questParticipants')
            ->findOrFail($id);

        // Verify quest belongs to current organization
        if ($quest->org_id !== $orgId) {
            abort(403, 'You do not have permission to view this quest.');
        }

        // Separate certificate and coupon prizes
        $certificatePrize = $quest->prizes()->where('type', 'CERTIFICATE')->first();
        $couponPrize = $quest->prizes()->where('type', 'COUPON')->first();

        return view('pages.organization.quests.show', compact('quest', 'certificatePrize', 'couponPrize', 'userOrganizations', 'currentOrg'));
    }

    
}
