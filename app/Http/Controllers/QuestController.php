<?php

namespace App\Http\Controllers;

use App\Http\Requests\Quest\CreateQuestRequest;
use App\Http\Requests\Quest\AddWinnersRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Organization;
use App\Models\OrganizationMember;
use App\Models\Prize;
use App\Models\Quest;
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
    public function readAll()
    {
        return view('pages.quests.index');
    }

    // Create quest
    public function create(CreateQuestRequest $request)
    {
        try {
            $organization = Organization::findOrFail($request->org_id);
            
            $isManager = OrganizationMember::where('organization_id', $organization->id)
                ->where('user_id', Auth::id())
                ->where('role', 'MANAGER')
                ->exists();

            $isCreator = $organization->created_by === Auth::id();

            if (!$isManager && !$isCreator) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to create quests for this organization.',
                ], 403);
            }

            DB::beginTransaction();

            $slug = Str::slug($request->title);
            $originalSlug = $slug;
            $counter = 1;
            
            while (Quest::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            // Handle banner upload
            $bannerUrl = null;
            if ($request->hasFile('banner')) {
                $bannerFile = $request->file('banner');
                $bannerName = time() . '_' . $bannerFile->getClientOriginalName();
                $bannerFile->storeAs('public/QuestStorage/Banner/' . $bannerName);
                $bannerUrl = '/storage/QuestStorage/Banner/' . $bannerName;
            }

            // Create quest
            $quest = Quest::create([
                'title' => $request->title,
                'slug' => $slug,
                'desc' => $request->desc,
                'banner_url' => $bannerUrl,
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
            $certImageUrl = null;
            if ($request->hasFile('cert_image')) {
                $certFile = $request->file('cert_image');
                $certName = time() . '_cert_' . $certFile->getClientOriginalName();
                $certFile->storeAs('public/PrizeStorage/' . $certName);
                $certImageUrl = '/storage/PrizeStorage/' . $certName;
            }

            // Create certificate prize
            $certificatePrize = Prize::create([
                'name' => $request->cert_name,
                'type' => 'CERTIFICATE',
                'description' => $request->cert_description,
                'image_url' => $certImageUrl,
                'quest_id' => $quest->id,
            ]);

            // Create coupon prize if provided
            $couponPrize = null;
            if ($request->coupon_name) {
                $couponImageUrl = null;
                if ($request->hasFile('coupon_image')) {
                    $couponFile = $request->file('coupon_image');
                    $couponName = time() . '_coupon_' . $couponFile->getClientOriginalName();
                    $couponFile->storeAs('public/PrizeStorage/' . $couponName);
                    $couponImageUrl = '/storage/PrizeStorage/' . $couponName;
                }

                $couponPrize = Prize::create([
                    'name' => $request->coupon_name,
                    'type' => 'COUPON',
                    'description' => $request->coupon_description,
                    'image_url' => $couponImageUrl,
                    'quest_id' => $quest->id,
                ]);
            }

            DB::commit();

            Log::info('Quest created successfully', [
                'quest_id' => $quest->id,
                'organization_id' => $organization->id,
                'created_by' => Auth::id(),
            ]);

            $prizes = [$certificatePrize];
            if ($couponPrize) {
                $prizes[] = $couponPrize;
            }

            return response()->json([
                'success' => true,
                'message' => 'Quest created successfully. Waiting for admin approval.',
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
                        'prizes' => collect($prizes)->map(function ($prize) {
                            return [
                                'id' => $prize->id,
                                'name' => $prize->name,
                                'type' => $prize->type,
                            ];
                        }),
                        'created_at' => $quest->created_at,
                    ],
                ],
            ], 201);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Organization not found.',
            ], 404);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create quest', [
                'user_id' => Auth::id(),
                'org_id' => $request->org_id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create quest. Please try again.',
            ], 500);
        }
    }

    // Delete quest
    public function destroy($id): ApiResponse
    {
        $quest = Quest::find($id);

        if (!$quest) {
            return ApiResponse::notFound('Quest not found');
        }

        $quest->delete();

        return ApiResponse::success(
            [],
            'Quest deleted successfully'
        );
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
                    Log::warning("Winner user_id {$winner->user_id} tidak punya wallet_address, skip.");
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
            Log::error('Distribute rewards failed: ' . $e->getMessage());
            
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

    
}
