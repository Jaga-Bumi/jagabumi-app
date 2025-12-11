<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrganizationRequest\StoreOrganizationRequestRequest;
use App\Models\OrganizationRequest;
use App\Models\OrganizationMember;
use Illuminate\Support\Facades\Auth;

class OrganizationRequestController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('home')->with('error', 'Please login first');
        }

        // Get the latest request with approver info
        $latestRequest = OrganizationRequest::where('user_id', $user->id)
            ->with('approver:id,name,handle')
            ->latest()
            ->first();

        $canSubmit = !$latestRequest || $latestRequest->status === 'REJECTED';

        return view('pages.join-us', compact('latestRequest', 'canSubmit'));
    }

    public function status()
    {
        $user = Auth::user();
        
        $existingRequest = OrganizationRequest::where('user_id', $user->id)
            ->with('approver:id,name,handle')
            ->latest()
            ->first();

        $isMember = OrganizationMember::where('user_id', $user->id)->exists();

        return response()->json([
            'can_submit' => !$existingRequest || $existingRequest->status === 'REJECTED',
            'has_request' => $existingRequest ? true : false,
            'request' => $existingRequest ? [
                'id' => $existingRequest->id,
                'status' => $existingRequest->status,
                'organization_name' => $existingRequest->organization_name,
                'organization_description' => $existingRequest->organization_description,
                'organization_type' => $existingRequest->organization_type,
                'website_url' => $existingRequest->website_url,
                'instagram_url' => $existingRequest->instagram_url,
                'x_url' => $existingRequest->x_url,
                'facebook_url' => $existingRequest->facebook_url,
                'reason' => $existingRequest->reason,
                'planned_activities' => $existingRequest->planned_activities,
                'admin_notes' => $existingRequest->admin_notes,
                'approver' => $existingRequest->approver,
                'created_at' => $existingRequest->created_at,
                'responded_at' => $existingRequest->responded_at,
            ] : null,
            'is_member' => $isMember,
            'can_create_organization' => $existingRequest && $existingRequest->status === 'APPROVED',
        ], 200);
    }

    public function store(StoreOrganizationRequestRequest $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('home')->with('error', 'Please login first');
        }

        $existingRequest = OrganizationRequest::where('user_id', $user->id)
            ->whereIn('status', ['PENDING', 'APPROVED'])
            ->first();

        if ($existingRequest) {
            return redirect()->route('join-us')
                ->with('error', 'You already have a ' . strtolower($existingRequest->status) . ' request.');
        }
            
        // Create new organization request
        $orgRequest = OrganizationRequest::create([
            'user_id' => $user->id,
            'organization_name' => $request->organization_name,
            'organization_description' => $request->organization_description,
            'organization_type' => $request->organization_type,
            'website_url' => $request->website_url,
            'instagram_url' => $request->instagram_url,
            'x_url' => $request->x_url,
            'facebook_url' => $request->facebook_url,
            'reason' => $request->reason,
            'planned_activities' => $request->planned_activities,
        ]);

        return redirect()->route('join-us')
            ->with('success', 'Request successfully submitted. Admin will review your request.');
    }

    public function show()
    {
        $user = Auth::user();
        
        $request = OrganizationRequest::where('user_id', $user->id)
            ->latest()
            ->first();

        if (!$request) {
            return response()->json([
                'success' => false,
                'message' => 'No request found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'request' => [
                'id' => $request->id,
                'status' => $request->status,
                'organization_name' => $request->organization_name,
                'organization_description' => $request->organization_description,
                'organization_type' => $request->organization_type,
                'website_url' => $request->website_url,
                'instagram_url' => $request->instagram_url,
                'x_url' => $request->x_url,
                'facebook_url' => $request->facebook_url,
                'reason' => $request->reason,
                'planned_activities' => $request->planned_activities,
                'admin_notes' => $request->admin_notes,
                'created_at' => $request->created_at,
                'responded_at' => $request->responded_at,
            ]
        ], 200);
    }

}
