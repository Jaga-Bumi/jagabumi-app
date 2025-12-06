<?php

namespace App\Http\Controllers;

use App\Http\Requests\Organization\CreateOrganizationRequest;
use App\Models\Organization;
use App\Models\OrganizationMember;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class OrganizationController extends Controller
{
    // Create new organization
    public function create(CreateOrganizationRequest $request)
    {
        $userId = Auth::id();

        // Check if user is a CREATOR of any organization
        $isCreator = OrganizationMember::where('user_id', $userId)->where('role', 'CREATOR')->exists();
        if ($isCreator) {
            return response()->json([
                'success' => false,
                'message' => 'You are already a CREATOR of an organization.'
            ], 403);
        }

        $bannerPath = $request->file('banner_img');
        $bannerName = Str::uuid() . '_' . str_replace(' ', '_', $bannerPath->getClientOriginalName());
        $bannerPath->storeAs('public/OrganizationStorage/Banner/' . $bannerName);

        $logoPath = $request->file('logo_img');
        $logoName = Str::uuid() . '_' . str_replace(' ', '_', $logoPath->getClientOriginalName());
        $logoPath->storeAs('public/OrganizationStorage/Logo/' . $logoName);

        $organization = Organization::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . Str::random(6),
            'handle' => $request->handle,
            'org_email' => $request->org_email,
            'desc' => $request->desc,
            'motto' => $request->motto,
            'banner_img' => $bannerName,
            'logo_img' => $logoName,
            'website_url' => $request->website_url,
            'instagram_url' => $request->instagram_url,
            'x_url' => $request->x_url,
            'facebook_url' => $request->facebook_url,
            'created_by' => $userId,
        ]);

        // Add creator as member with role CREATOR
        OrganizationMember::create([
            'organization_id' => $organization->id,
            'user_id' => $userId,
            'role' => 'CREATOR',
            'joined_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'data' => $organization,
        ]);
    }
}
