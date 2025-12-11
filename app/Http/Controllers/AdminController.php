<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Organization;
use App\Models\OrganizationRequest;
use App\Models\Quest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    // Dashboard view
    public function dashboard()
    {
        $pendingOrgRequests = OrganizationRequest::where('status', 'PENDING')->count();
        $questsInReview = Quest::where('status', 'IN REVIEW')->count();
        $totalUsers = User::count();
        $totalOrganizations = Organization::where('status', 'ACTIVE')->count();
        
        $recentOrgRequests = OrganizationRequest::with('user:id,name,handle')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        $recentQuests = Quest::with('organization:id,name')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('pages.tests.admin.index', compact(
            'pendingOrgRequests',
            'questsInReview',
            'totalUsers',
            'totalOrganizations',
            'recentOrgRequests',
            'recentQuests'
        ));
    }

    // Organization requests view
    public function organizationRequestsView(Request $request)
    {
        $status = $request->query('status', 'all');
        
        $query = OrganizationRequest::with(['user:id,name,email,handle,wallet_address'])
            ->orderBy('created_at', 'desc');

        if ($status !== 'all' && in_array(strtoupper($status), ['PENDING', 'APPROVED', 'REJECTED'])) {
            $query->where('status', strtoupper($status));
        }

        $requests = $query->paginate(10);

        return view('pages.tests.admin.organization-requests', compact('requests'));
    }

    // Quests view
    public function questsView(Request $request)
    {
        $status = $request->query('status', 'all');
        
        $query = Quest::with(['organization:id,name,slug,logo_img', 'prizes:id,quest_id,name,type'])
            ->orderBy('created_at', 'desc');

        if ($status !== 'all' && in_array(strtoupper(str_replace('_', ' ', $status)), ['IN REVIEW', 'APPROVED', 'REJECTED', 'ACTIVE', 'ENDED', 'CANCELLED'])) {
            $query->where('status', strtoupper(str_replace('_', ' ', $status)));
        }

        $quests = $query->paginate(10);

        return view('pages.tests.admin.quests', compact('quests'));
    }

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
                    'organization_type' => $req->organization_type,
                    'organization_description' => $req->organization_description,
                    'reason' => $req->reason,
                    'planned_activities' => $req->planned_activities,
                    'website_url' => $req->website_url,
                    'instagram_url' => $req->instagram_url,
                    'x_url' => $req->x_url,
                    'facebook_url' => $req->facebook_url,
                    'admin_notes' => $req->admin_notes,
                    'created_at' => $req->created_at,
                    'responded_at' => $req->responded_at,
                    'user' => [
                        'id' => $req->user->id,
                        'name' => $req->user->name,
                        'email' => $req->user->email,
                        'handle' => $req->user->handle,
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
                'organization_type' => $request->organization_type,
                'organization_description' => $request->organization_description,
                'reason' => $request->reason,
                'planned_activities' => $request->planned_activities,
                'website_url' => $request->website_url,
                'instagram_url' => $request->instagram_url,
                'x_url' => $request->x_url,
                'facebook_url' => $request->facebook_url,
                'admin_notes' => $request->admin_notes,
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
    public function approveOrganizationRequest(Request $request, $id)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
        ]);

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
            'admin_notes' => $request->input('admin_notes'),
        ]);

        return redirect()->route('admin.organization-requests')
            ->with('success', 'Organization request approved successfully: ' . $orgRequest->organization_name);
        
    }

    // Reject organization request
    public function rejectOrganizationRequest(Request $request, $id)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
        ]);

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
            'admin_notes' => $request->input('admin_notes'),
        ]);

        return redirect()->route('admin.organization-requests')
            ->with('success', 'Organization request rejected: ' . $orgRequest->organization_name);
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
            return redirect()->route('admin.quests')
                ->with('error', 'Quest not found.');
        }

        if ($quest->status !== 'IN REVIEW') {
            return redirect()->route('admin.quests')
                ->with('error', 'Only quests with IN REVIEW status can be approved. Current status: ' . $quest->status);
        }

        $quest->update([
            'status' => 'APPROVED',
            'approval_date' => now(),
        ]);

        return redirect()->route('admin.quests')
            ->with('success', 'Quest approved successfully: ' . $quest->title);
    }

    // Reject quest
    public function rejectQuest($id)
    {
        $quest = Quest::find($id);

        if (!$quest) {
            return redirect()->route('admin.quests')
                ->with('error', 'Quest not found.');
        }

        if ($quest->status !== 'IN REVIEW') {
            return redirect()->route('admin.quests')
                ->with('error', 'Only quests with IN REVIEW status can be rejected. Current status: ' . $quest->status);
        }

        $quest->update([
            'status' => 'REJECTED',
        ]);

        return redirect()->route('admin.quests')
            ->with('success', 'Quest rejected: ' . $quest->title);
    }

    public function removeUser($userId) // logic remove image avatar belum
    {
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $user->is_removed = true;
        $user->save();

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

    public function removeArticle($articleId)
    {
        $article = Article::find($articleId);
        if (!$article) {
            return response()->json(['error' => 'Article not found'], 404);
        }

        // Soft delete
        $article->is_deleted = true;
        $article->save();

        return response()->json(['message' => 'Article removed successfully'], 200);
    }

}