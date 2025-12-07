<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\OrganizationRequest;
use App\Models\Quest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    // Organization request management functions
    public function getOrganizationRequests(Request $request)
    {
        $status = $request->query('status', 'all');
        
        $query = OrganizationRequest::with(['user:id,name,email,handle,phone,wallet_address'])
            ->orderBy('created_at', 'desc');

        if ($status !== 'all' && in_array(strtoupper($status), ['PENDING', 'APPROVED', 'REJECTED'])) {
            $query->where('status', strtoupper($status));
        }

        $requests = $query->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $requests->map(function ($req) {
                return [
                    'id' => $req->id,
                    'status' => $req->status,
                    'organization_name' => $req->organization_name,
                    'organization_description' => $req->organization_description,
                    'phone_number' => $req->phone_number,
                    'email' => $req->email,
                    'reason' => $req->reason,
                    'created_at' => $req->created_at,
                    'responded_at' => $req->responded_at,
                    'user' => [
                        'id' => $req->user->id,
                        'name' => $req->user->name,
                        'email' => $req->user->email,
                        'handle' => $req->user->handle,
                        'phone' => $req->user->phone,
                        'wallet_address' => $req->user->wallet_address,
                    ],
                ];
            }),
            'pagination' => [
                'current_page' => $requests->currentPage(),
                'last_page' => $requests->lastPage(),
                'per_page' => $requests->perPage(),
                'total' => $requests->total(),
            ]
        ]);
    }

    // Get organization request detail
    public function getOrganizationRequestDetail($id)
    {
        $request = OrganizationRequest::with(['user:id,name,email,handle,phone,wallet_address,bio,avatar_url,created_at'])
            ->find($id);

        if (!$request) {
            return response()->json([
                'success' => false,
                'message' => 'Organization request not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $request->id,
                'status' => $request->status,
                'organization_name' => $request->organization_name,
                'organization_description' => $request->organization_description,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'reason' => $request->reason,
                'created_at' => $request->created_at,
                'responded_at' => $request->responded_at,
                'approved_by' => $request->approved_by,
                'user' => [
                    'id' => $request->user->id,
                    'name' => $request->user->name,
                    'email' => $request->user->email,
                    'handle' => $request->user->handle,
                    'phone' => $request->user->phone,
                    'wallet_address' => $request->user->wallet_address,
                    'bio' => $request->user->bio,
                    'avatar_url' => $request->user->avatar_url,
                    'member_since' => $request->user->created_at,
                ],
            ]
        ]);
    }

    // Approve organization request
    public function approveOrganizationRequest($id)
    {
        $orgRequest = OrganizationRequest::find($id);

        if (!$orgRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Organization request not found.',
            ], 404);
        }

        // Check if already processed
        if ($orgRequest->status !== 'PENDING') {
            return response()->json([
                'success' => false,
                'message' => 'This request has already been ' . strtolower($orgRequest->status) . '.',
            ], 400);
        }

        $orgRequest->update([
            'status' => 'APPROVED',
            'approved_by' => Auth::id(),
            'responded_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Organization request approved successfully.',
            'data' => [
                'id' => $orgRequest->id,
                'status' => $orgRequest->status,
                'organization_name' => $orgRequest->organization_name,
                'responded_at' => $orgRequest->responded_at,
            ]
        ]);
        
    }

    // Reject organization request
    public function rejectOrganizationRequest($id)
    {
        $orgRequest = OrganizationRequest::find($id);

        if (!$orgRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Organization request not found.',
            ], 404);
        }

        // Check if already processed
        if ($orgRequest->status !== 'PENDING') {
            return response()->json([
                'success' => false,
                'message' => 'This request has already been ' . strtolower($orgRequest->status) . '.',
            ], 400);
        }

        $orgRequest->update([
            'status' => 'REJECTED',
            'approved_by' => Auth::id(),
            'responded_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Organization request rejected.',
            'data' => [
                'id' => $orgRequest->id,
                'status' => $orgRequest->status,
                'organization_name' => $orgRequest->organization_name,
                'responded_at' => $orgRequest->responded_at,
            ]
        ]);
    }

    // Quest management functions
    public function getQuests(Request $request)
    {
        $status = $request->query('status', 'all');
        
        $query = Quest::with(['organization:id,name,slug,logo_img', 'prizes:id,quest_id,name,type'])
            ->orderBy('created_at', 'desc');

        if ($status !== 'all' && in_array(strtoupper(str_replace('_', ' ', $status)), ['IN REVIEW', 'APPROVED', 'REJECTED', 'ACTIVE', 'ENDED', 'CANCELLED'])) {
            $query->where('status', strtoupper(str_replace('_', ' ', $status)));
        }

        $quests = $query->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $quests->map(function ($quest) {
                return [
                    'id' => $quest->id,
                    'title' => $quest->title,
                    'slug' => $quest->slug,
                    'status' => $quest->status,
                    'location_name' => $quest->location_name,
                    'participant_limit' => $quest->participant_limit,
                    'winner_limit' => $quest->winner_limit,
                    'registration_start_at' => $quest->registration_start_at,
                    'quest_start_at' => $quest->quest_start_at,
                    'approval_date' => $quest->approval_date,
                    'created_at' => $quest->created_at,
                    'organization' => [
                        'id' => $quest->organization->id,
                        'name' => $quest->organization->name,
                        'slug' => $quest->organization->slug,
                        'logo_img' => $quest->organization->logo_img,
                    ],
                    'prizes' => $quest->prizes->map(function ($prize) {
                        return [
                            'id' => $prize->id,
                            'name' => $prize->name,
                            'type' => $prize->type,
                        ];
                    }),
                ];
            }),
            'pagination' => [
                'current_page' => $quests->currentPage(),
                'last_page' => $quests->lastPage(),
                'per_page' => $quests->perPage(),
                'total' => $quests->total(),
            ]
        ]);
    }

    // Get quest detail
    public function getQuestDetail($id)
    {
        $quest = Quest::with([
            'organization:id,name,slug,logo_img,banner_img,desc',
            'prizes:id,quest_id,name,type,description,image_url'
        ])->find($id);

        if (!$quest) {
            return response()->json([
                'success' => false,
                'message' => 'Quest not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $quest->id,
                'title' => $quest->title,
                'slug' => $quest->slug,
                'desc' => $quest->desc,
                'banner_url' => $quest->banner_url,
                'status' => $quest->status,
                'location_name' => $quest->location_name,
                'latitude' => $quest->latitude,
                'longitude' => $quest->longitude,
                'radius_meter' => $quest->radius_meter,
                'liveness_code' => $quest->liveness_code,
                'registration_start_at' => $quest->registration_start_at,
                'registration_end_at' => $quest->registration_end_at,
                'quest_start_at' => $quest->quest_start_at,
                'quest_end_at' => $quest->quest_end_at,
                'judging_start_at' => $quest->judging_start_at,
                'judging_end_at' => $quest->judging_end_at,
                'prize_distribution_date' => $quest->prize_distribution_date,
                'participant_limit' => $quest->participant_limit,
                'winner_limit' => $quest->winner_limit,
                'approval_date' => $quest->approval_date,
                'created_at' => $quest->created_at,
                'updated_at' => $quest->updated_at,
                'organization' => [
                    'id' => $quest->organization->id,
                    'name' => $quest->organization->name,
                    'slug' => $quest->organization->slug,
                    'desc' => $quest->organization->desc,
                    'logo_img' => $quest->organization->logo_img,
                    'banner_img' => $quest->organization->banner_img,
                ],
                'prizes' => $quest->prizes->map(function ($prize) {
                    return [
                        'id' => $prize->id,
                        'name' => $prize->name,
                        'type' => $prize->type,
                        'description' => $prize->description,
                        'image_url' => $prize->image_url,
                    ];
                }),
            ],
        ]);
    }

    // Approve quest
    public function approveQuest($id)
    {
        $quest = Quest::find($id);

        if (!$quest) {
            return response()->json([
                'success' => false,
                'message' => 'Quest not found.',
            ], 404);
        }

        if ($quest->status !== 'IN REVIEW') {
            return response()->json([
                'success' => false,
                'message' => 'Only quests with IN REVIEW status can be approved. Current status: ' . $quest->status,
            ], 400);
        }

        $quest->update([
            'status' => 'APPROVED',
            'approval_date' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Quest approved successfully.',
            'data' => [
                'id' => $quest->id,
                'title' => $quest->title,
                'status' => $quest->status,
                'approval_date' => $quest->approval_date,
            ]
        ]);
    }

    // Reject quest
    public function rejectQuest($id)
    {
        $quest = Quest::find($id);

        if (!$quest) {
            return response()->json([
                'success' => false,
                'message' => 'Quest not found.',
            ], 404);
        }

        if ($quest->status !== 'IN REVIEW') {
            return response()->json([
                'success' => false,
                'message' => 'Only quests with IN REVIEW status can be rejected. Current status: ' . $quest->status,
            ], 400);
        }

        $quest->update([
            'status' => 'REJECTED',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Quest rejected.',
            'data' => [
                'id' => $quest->id,
                'title' => $quest->title,
                'status' => $quest->status,
            ]
        ]);
    }

    public function removeUser($userId) // logic remove image avatar belum
    {
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $user->delete();

        // avatar_url yang non social belum dihapus dari storage

        return response()->json(['message' => 'User removed successfully'], 200);
    }

    public function removeOrganization($orgId)
    {
        $organization = Organization::find($orgId);
        if (!$organization) {
            return response()->json(['error' => 'Organization not found'], 404);
        }

        // Turns to inactive
        $organization->status = 'INACTIVE';
        $organization->save();

        return response()->json(['message' => 'Organization removed successfully'], 200);
    }

}