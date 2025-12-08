<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrganizationMember\InviteMemberRequest;
use App\Models\Organization;
use App\Models\OrganizationMember;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class OrganizationMemberController extends Controller
{
    
    // Invite member
    public function invite(InviteMemberRequest $request, $organizationId)
    {
        $organization = Organization::findOrFail($organizationId);
        
        // Check authorization: only creator can invite
        if ($organization->created_by !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Only organization creator can invite members.'
            ], 403);
        }

        $invitedUser = User::where('email', $request->email)->first();

        if (!$invitedUser) {
            return response()->json([
                'success' => false,
                'message' => 'User with this email not found.'
            ], 404);
        }

        // Check if already member
        $existingMember = OrganizationMember::where('organization_id', $organizationId)
            ->where('user_id', $invitedUser->id)
            ->first();

        if ($existingMember) {
            return response()->json([
                'success' => false,
                'message' => 'User is already a member or has pending invitation.'
            ], 400);
        }

        // Create invitation
        OrganizationMember::create([
            'organization_id' => $organizationId,
            'user_id' => $invitedUser->id,
            'role' => 'MANAGER',
            'status' => 'PENDING',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Invitation sent successfully to ' . $invitedUser->name
        ], 201);
    }

    // Accept invitation
    public function acceptInvitation($membershipId)
    {
        $membership = OrganizationMember::findOrFail($membershipId);

        if ($membership->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.'
            ], 403);
        }

        if ($membership->status !== 'PENDING') {
            return response()->json([
                'success' => false,
                'message' => 'Invitation is no longer valid.'
            ], 400);
        }

        $membership->update([
            'status' => 'ACTIVE',
            'joined_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'You have joined the organization as ' . $membership->role . '.'
        ], 200);
    }

    // Decline invitattion
    public function declineInvitation($membershipId)
    {
        $membership = OrganizationMember::findOrFail($membershipId);

        if ($membership->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.'
            ], 403);
        }

        if ($membership->status !== 'PENDING') {
            return response()->json([
                'success' => false,
                'message' => 'Invitation is no longer valid.'
            ], 400);
        }

        $membership->delete();

        return response()->json([
            'success' => true,
            'message' => 'You have declined the invitation to join the organization.'
        ], 200);
    }

    // Remove member
    public function removeMember($membershipId)
    {
        $membership = OrganizationMember::findOrFail($membershipId);

        // Only creator can remove
        if ($membership->organization->created_by !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Only organization creator can remove members.'
            ], 403);
        }

        // Cannot remove CREATOR
        if ($membership->role === 'CREATOR') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot remove member with CREATOR role.'
            ], 400);
        }

        $membership->delete();

        return response()->json([
            'success' => true,
            'message' => 'Member removed successfully from organization.'
        ], 200);
    }

    public function leave($membershipId)
    {
        $membership = OrganizationMember::findOrFail($membershipId);

        if ($membership->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.'
            ], 403);
        }

        // Cannot leave if CREATOR
        if ($membership->role === 'CREATOR') {
            return response()->json([
                'success' => false,
                'message' => 'CREATOR cannot leave the organization.'
            ], 400);
        }

        $membership->delete();

        return response()->json([
            'success' => true,
            'message' => 'You have left the organization.'
        ], 200);
    }
}
