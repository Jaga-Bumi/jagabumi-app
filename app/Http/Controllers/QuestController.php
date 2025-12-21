<?php

namespace App\Http\Controllers;

use App\Http\Requests\Quest\CreateQuestRequest;
use App\Http\Requests\Quest\AddWinnersRequest;
use App\Http\Requests\Quest\UpdateQuestRequest;
use App\Models\Organization;
use App\Models\OrganizationMember;
use App\Models\Prize;
use App\Models\PrizeUser;
use App\Models\Quest;
use App\Models\QuestParticipant;
use App\Models\QuestWinner;
use App\Services\BlockchainService;
use App\Services\FilebaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage; // Added Storage Facade
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
                // UPDATED: Use Storage Facade
                $bannerFile->storeAs('QuestStorage/Banner', $bannerName, 'public');
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
                // UPDATED: Use Storage Facade
                $certFile->storeAs('PrizeStorage', $certImageName, 'public');
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
                    // UPDATED: Use Storage Facade
                    $couponFile->storeAs('PrizeStorage', $couponImageName, 'public');
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
                // UPDATED: Use Storage Facade
                Storage::disk('public')->delete('QuestStorage/Banner/' . $quest->banner_url);
            }

            foreach ($quest->prizes as $prize) {
                if ($prize->image_url) {
                    // UPDATED: Use Storage Facade
                    Storage::disk('public')->delete('PrizeStorage/' . $prize->image_url);
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
                    // UPDATED: Use Storage Facade
                    Storage::disk('public')->delete('QuestStorage/Banner/' . $quest->banner_url);
                }
                
                $bannerFile = $request->file('banner');
                $bannerName = Str::uuid() . '_' . str_replace(' ', '_', $bannerFile->getClientOriginalName());
                // UPDATED: Use Storage Facade
                $bannerFile->storeAs('QuestStorage/Banner', $bannerName, 'public');
                
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
                    // UPDATED: Use Storage Facade
                    Storage::disk('public')->delete('PrizeStorage/' . $certImageName);
                }
                
                $certFile = $request->file('cert_image');
                $certImageName = Str::uuid() . '_cert_' . str_replace(' ', '_', $certFile->getClientOriginalName());
                // UPDATED: Use Storage Facade
                $certFile->storeAs('PrizeStorage', $certImageName, 'public');
                
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
                        // UPDATED: Use Storage Facade
                        Storage::disk('public')->delete('PrizeStorage/' . $couponImageName);
                    }
                    
                    $couponFile = $request->file('coupon_image');
                    $couponImageName = Str::uuid() . '_coupon_' . str_replace(' ', '_', $couponFile->getClientOriginalName());
                    // UPDATED: Use Storage Facade
                    $couponFile->storeAs('PrizeStorage', $couponImageName, 'public');

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
    public function distributeRewards(Request $request, BlockchainService $blockchain, FilebaseService $filebase)
    {
        $questId = $request->input('quest_id');
        
        if (!$questId) {
            return response()->json([
                'success' => false,
                'message' => 'Quest ID is required'
            ], 400);
        }

        DB::beginTransaction();
        try {
            $quest = Quest::with(['prizes', 'questWinners.user', 'organization'])->findOrFail($questId);

            // Check authorization via OrganizationMember
            $user = Auth::user();
            $isOrgMember = OrganizationMember::where('user_id', $user->id)
                ->where('organization_id', $quest->org_id)
                ->where('status', 'ACTIVE')
                ->exists();
                
            if (!$isOrgMember) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to distribute prizes for this quest.'
                ], 403);
            }

            if (!in_array($quest->status, ['APPROVED', 'ACTIVE', 'ENDED'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Quest must be APPROVED, ACTIVE, or ENDED to distribute rewards.'
                ], 400);
            }

            $winners = $quest->questWinners()->where('reward_distributed', false)->with('user')->get();
            
            if ($winners->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No pending winners to distribute rewards to.'
                ], 400);
            }

            // Get ALL prizes for this quest (both CERTIFICATE and COUPON)
            $prizes = $quest->prizes;
            
            if ($prizes->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No prizes configured for this quest.'
                ], 400);
            }

            $recipients = [];
            $uris = [];
            $prizeUserRecords = [];

            // Process each winner × each prize combination
            foreach ($winners as $winner) {
                if (!$winner->user || !$winner->user->wallet_address) {
                    continue;
                }

                foreach ($prizes as $prize) {
                    // Get raw image_url from database (not the accessor-modified one)
                    $rawImageUrl = $prize->getAttributes()['image_url'] ?? $prize->getRawOriginal('image_url');
                    
                    Log::info("Prize {$prize->id} raw image_url: " . $rawImageUrl);
                    
                    // Check if image needs to be uploaded to IPFS
                    $imageCid = $rawImageUrl;
                    if (!$rawImageUrl || (!str_starts_with($rawImageUrl, 'Qm') && !str_starts_with($rawImageUrl, 'bafy'))) {
                        // Extract filename from URL if it's a full URL
                        $imageFilename = $rawImageUrl;
                        
                        // If it's a URL, extract just the filename
                        if (str_contains($rawImageUrl, '/')) {
                            // Get everything after the last /
                            $imageFilename = basename(parse_url($rawImageUrl, PHP_URL_PATH));
                        }
                        
                        Log::info("Extracted filename: " . $imageFilename);
                        
                        // Image is a local file, need to upload to IPFS
                        // UPDATED: Point to storage/app/public/PrizeStorage since we are using Storage facade
                        $imagePath = storage_path('app/public/PrizeStorage/' . $imageFilename);
                        
                        if (file_exists($imagePath)) {
                            Log::info("Uploading prize image from: " . $imagePath);
                            
                            $imageContent = file_get_contents($imagePath);
                            $extension = pathinfo($imagePath, PATHINFO_EXTENSION) ?: 'png';
                            $mimeType = $extension === 'jpg' || $extension === 'jpeg' ? 'image/jpeg' : 'image/' . $extension;
                            $imageFileName = "prizes/quest_{$questId}_{$prize->type}_{$prize->id}_" . time() . "." . $extension;
                            
                            $imageCid = $filebase->uploadToIpfs($imageContent, $imageFileName, $mimeType);
                            
                            if (!$imageCid) {
                                Log::warning("Failed to upload prize image to IPFS for {$prize->name}, using placeholder");
                                $imageCid = "placeholder"; // Use placeholder if upload fails
                            } else {
                                // Update prize with IPFS CID for future use
                                $prize->update(['image_url' => $imageCid]);
                                Log::info("Prize image uploaded to IPFS: " . $imageCid);
                            }
                        } else {
                            Log::warning("Prize image file not found at: " . $imagePath);
                            $imageCid = "placeholder"; // Use placeholder if file not found
                        }
                    }

                    // Create metadata for NFT
                    $metadata = [
                        'name' => $prize->name,
                        'description' => $prize->description ?? "{$prize->type} for {$quest->title}",
                        'image' => "ipfs://{$imageCid}",
                        'attributes' => [
                            [
                                'trait_type' => 'Quest',
                                'value' => $quest->title
                            ],
                            [
                                'trait_type' => 'Type',
                                'value' => $prize->type
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
                    $metadataFileName = "metadata/quest_{$questId}_prize_{$prize->id}_winner_{$winner->user_id}_" . time() . ".json";
                    $metadataCid = $filebase->uploadToIpfs(
                        json_encode($metadata), 
                        $metadataFileName, 
                        'application/json'
                    );

                    if (!$metadataCid) {
                        throw new \Exception("Failed to upload metadata for {$winner->user->name}");
                    }

                    $recipients[] = $winner->user->wallet_address;
                    $uris[] = "ipfs://{$metadataCid}";
                    
                    // Prepare prize_users record
                    $prizeUserRecords[] = [
                        'prize_id' => $prize->id,
                        'user_id' => $winner->user_id,
                        'token_uri' => "ipfs://{$metadataCid}",
                    ];
                }
            }

            if (empty($recipients)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No winners with valid wallet addresses found.'
                ], 400);
            }

            // Mint all NFTs in one batch transaction
            $txHash = $blockchain->mintBatch($recipients, $uris);

            if (!$txHash) {
                throw new \Exception("Failed to mint NFTs - no transaction hash returned.");
            }

            // Update winners as distributed
            foreach ($winners as $winner) {
                if ($winner->user && $winner->user->wallet_address) {
                    $winner->update([
                        'reward_distributed' => true,
                        'tx_hash' => $txHash,
                        'distributed_at' => now()
                    ]);
                }
            }

            // Create prize_users records
            foreach ($prizeUserRecords as $record) {
                PrizeUser::create([
                    'prize_id' => $record['prize_id'],
                    'user_id' => $record['user_id'],
                    'token_uri' => $record['token_uri'],
                    'tx_hash' => $txHash,
                ]);
            }

            // Update quest status to ENDED if still ACTIVE
            if ($quest->status === 'ACTIVE') {
                $quest->update(['status' => 'ENDED']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Prizes distributed to ' . count($winners) . ' winners (' . count($recipients) . ' NFTs minted)',
                'transaction_hash' => $txHash,
                'recipients_count' => count($recipients),
                'nfts_minted' => count($uris),
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Prize distribution failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to distribute prizes: ' . $e->getMessage()
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

    public function updateStatus($questId)
    {
        $quest = Quest::findOrFail($questId);
        $user = Auth::user();

        // Check if user has access (is member of the organization)
        $isMember = OrganizationMember::where('user_id', $user->id)
            ->where('organization_id', $quest->org_id)
            ->where('status', 'ACTIVE')
            ->whereIn('role', ['CREATOR', 'MANAGER'])
            ->exists();

        if (!$isMember) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to update this quest.'
            ], 403);
        }

        // Only allow status change if quest is not IN REVIEW or REJECTED
        if (in_array($quest->status, ['IN REVIEW', 'REJECTED'])) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot change status of quests that are IN REVIEW or REJECTED.'
            ], 400);
        }

        $request = request();
        $validStatuses = ['ACTIVE', 'ENDED', 'CANCELLED'];
        
        if (!$request->has('status') || !in_array($request->status, $validStatuses)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid status. Must be ACTIVE, ENDED, or CANCELLED.'
            ], 400);
        }

        $quest->status = $request->status;
        $quest->save();

        return response()->json([
            'success' => true,
            'message' => 'Quest status updated to ' . $request->status,
            'status' => $request->status
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

        // Include APPROVED, ACTIVE, and ENDED quests that have winners
        $quests = Quest::where('org_id', $orgId)
            ->whereIn('status', ['APPROVED', 'ACTIVE', 'ENDED'])
            ->whereHas('questWinners')
            ->with(['prizes', 'questWinners.user'])
            ->withCount(['questWinners' => function($q) {
                $q->where('reward_distributed', false);
            }])
            ->get()
            ->map(function($quest) {
                // Transform questWinners to include prize_status and rename to 'winners' for frontend
                $quest->winners = $quest->questWinners->map(function($winner) {
                    $winner->prize_status = $winner->reward_distributed ? 'DISTRIBUTED' : 'PENDING';
                    return $winner;
                });
                $quest->winners_count = $quest->quest_winners_count;
                return $quest;
            });

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