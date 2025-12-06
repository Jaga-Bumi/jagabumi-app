<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrganizationMember\InviteMemberRequest;
use App\Models\Organization;
use App\Models\OrganizationMember;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrganizationMemberController extends Controller
{
    
    // Invite member
    public function invite(InviteMemberRequest $request, $organizationId)
    {
        $organization = Organization::findOrFail($organizationId);
        
        // Check authorization: hanya creator yang bisa invite
        if ($organization->created_by !== Auth::id()) {
            abort(403, 'Hanya founder organisasi yang bisa mengundang member.');
        }

        $invitedUser = User::where('email', $request->email)->first();

        // Create invitation
        OrganizationMember::create([
            'organization_id' => $organizationId,
            'user_id' => $invitedUser->id,
            'role' => 'MANAGER',
            'status' => 'PENDING',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Undangan berhasil dikirim ke ' . $invitedUser->name
        ]);
    }

    // Accept invitation
    public function acceptInvitation($membershipId)
    {
        $membership = OrganizationMember::findOrFail($membershipId);

        if ($membership->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        if ($membership->status !== 'PENDING') {
            return response()->json([
                'success' => false,
                'message' => 'Invitation sudah tidak valid.'
            ], 400);
        }

        $membership->update([
            'status' => 'ACTIVE',
            'joined_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Anda telah bergabung dengan organisasi sebagai ' . $membership->role . '.'
        ]);
    }

    // Remove member
    public function removeMember($membershipId)
    {
        $membership = OrganizationMember::findOrFail($membershipId);

        // Only creator can remove
        if ( $membership->organization->created_by !== Auth::id()) {
            abort(403);
        }

        // Cannot remove MAKER
        if ($membership->role === 'CREATOR') {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus member dengan peran CREATOR.'
            ], 400);
        }

        $membership->delete();

        return response()->json([
            'success' => true,
            'message' => 'Member berhasil dihapus dari organisasi.'
        ]);
    }

    public function leave($membershipId)
    {
        $membership = OrganizationMember::findOrFail($membershipId);

        if ($membership->user_id !== Auth::id()) {
            abort(403);
        }

        // Cannot leave if CREATOR
        if ($membership->role === 'CREATOR') {
            return response()->json([
                'success' => false,
                'message' => 'CREATOR tidak dapat meninggalkan organisasi.'
            ], 400);
        }

        $membership->delete();

        return response()->json([
            'success' => true,
            'message' => 'Anda telah meninggalkan organisasi.'
        ]);
    }
}
