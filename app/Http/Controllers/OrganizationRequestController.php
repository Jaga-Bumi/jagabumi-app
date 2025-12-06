<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrganizationRequest\StoreOrganizationRequestRequest;
use App\Models\OrganizationRequest;
use App\Models\OrganizationMember;
use Illuminate\Support\Facades\Auth;

class OrganizationRequestController extends Controller
{

    public function status()
    {
        $user = Auth::user();
        
        $existingRequest = OrganizationRequest::where('user_id', $user->id)
            ->whereIn('status', ['PENDING', 'APPROVED', 'REJECTED'])
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
                'phone_number' => $existingRequest->phone_number,
                'email' => $existingRequest->email,
                'reason' => $existingRequest->reason,
                'created_at' => $existingRequest->created_at,
                'responded_at' => $existingRequest->responded_at,
            ] : null,
            'is_member' => $isMember,
            'can_create_organization' => $existingRequest && $existingRequest->status === 'APPROVED',
        ]);
    }

    public function store(StoreOrganizationRequestRequest $request)
    {
        $user = Auth::user();

        $existingRequest = OrganizationRequest::where('user_id', $user->id)
            ->whereIn('status', ['PENDING', 'APPROVED'])
            ->first();

        if ($existingRequest) {
            return response()->json([
                'success' => false,
                'message' => 'You already have a ' . strtolower($existingRequest->status) . ' request.',
                'request' => [
                    'id' => $existingRequest->id,
                    'status' => $existingRequest->status,
                    'organization_name' => $existingRequest->organization_name,
                    'created_at' => $existingRequest->created_at,
                ]
            ], 400);
        }
            
        // Create new organization request
        $orgRequest = OrganizationRequest::create([
            'user_id' => $user->id,
            'organization_name' => $request->organization_name,
            'organization_description' => $request->organization_description,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'reason' => $request->reason,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Request successfully submitted. Admin will review your request.',
            'request' => [
                'id' => $orgRequest->id,
                'status' => $orgRequest->status,
                'organization_name' => $orgRequest->organization_name,
                'created_at' => $orgRequest->created_at,
            ]
        ], 201);
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
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'reason' => $request->reason,
                'created_at' => $request->created_at,
                'responded_at' => $request->responded_at,
            ]
        ]);
    }

    public function getLastRequest()
    {
        $lastRequest = OrganizationRequest::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->first();

        return $lastRequest;
    }
}
