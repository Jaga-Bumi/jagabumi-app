<?php

namespace App\Http\Controllers;

use App\Http\Requests\Organization\CreateOrganizationRequest;
use App\Http\Requests\Organization\UpdateOrganizationRequest;
use App\Models\Organization;
use App\Models\OrganizationMember;
use App\Models\OrganizationRequest;
use App\Models\Quest;
use App\Models\QuestParticipant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrganizationController extends Controller
{
    // Dashboard - Show organization overview
    public function dashboard()
    {
        $user = Auth::user();
        
        // Get user's organizations with role
        $userOrganizations = OrganizationMember::where('user_id', $user->id)
            ->where('status', 'ACTIVE')
            ->with('organization')
            ->get()
            ->map(function($member) {
                return [
                    'id' => $member->organization->id,
                    'name' => $member->organization->name,
                    'slug' => $member->organization->slug,
                    'role' => $member->role,
                ];
            });

        // Check if user has approved organization request but hasn't created org yet
        $canCreateOrg = false;
        $approvedRequest = null;
        
        if (!$user->createdOrganization) {
            $approvedRequest = OrganizationRequest::where('user_id', $user->id)
                ->where('status', 'APPROVED')
                ->latest()
                ->first();
            $canCreateOrg = $approvedRequest !== null;
        }

        // Get current organization (first one or from session)
        $firstOrg = $userOrganizations->first();
        $currentOrgId = session('current_org_id', $firstOrg['id'] ?? null);
        $currentOrg = $userOrganizations->firstWhere('id', $currentOrgId);
        
        if (!$currentOrg && !$canCreateOrg) {
            return redirect()->route('home')->with('error', 'You are not a member of any organization');
        }

        if ($canCreateOrg && !$currentOrg) {
            return view('pages.organization.create', compact('approvedRequest', 'userOrganizations'));
        }

        // Get organization statistics
        $organization = Organization::findOrFail($currentOrg['id']);
        
        $stats = [
            'active_quests' => Quest::where('org_id', $organization->id)
                ->where('status', 'ACTIVE')
                ->count(),
            'total_participants' => QuestParticipant::whereHas('quest', function($q) use ($organization) {
                $q->where('org_id', $organization->id);
            })->distinct('user_id')->count(),
            'prizes_distributed' => QuestParticipant::whereHas('quest', function($q) use ($organization) {
                $q->where('org_id', $organization->id);
            })->where('status', 'APPROVED')->count(),
            'total_quests' => Quest::where('org_id', $organization->id)->count(),
        ];

        // Get recent quests
        $recentQuests = Quest::where('org_id', $organization->id)
            ->withCount('questParticipants')
            ->latest()
            ->take(5)
            ->get();

        // Get recent submissions for review
        $recentSubmissions = QuestParticipant::whereHas('quest', function($q) use ($organization) {
            $q->where('org_id', $organization->id);
        })
        ->where('status', 'COMPLETED')
        ->with(['user', 'quest'])
        ->latest('submission_date')
        ->take(5)
        ->get();

        // Participant growth data (last 6 months)
        $participantData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $count = QuestParticipant::whereHas('quest', function($q) use ($organization) {
                $q->where('org_id', $organization->id);
            })
            ->whereMonth('created_at', $month->month)
            ->whereYear('created_at', $month->year)
            ->count();
            
            $participantData[] = [
                'month' => $month->format('M'),
                'participants' => $count,
            ];
        }

        return view('pages.organization.dashboard', compact(
            'organization',
            'userOrganizations',
            'currentOrg',
            'stats',
            'recentQuests',
            'recentSubmissions',
            'participantData'
        ));
    }

    // Switch organization
    public function switchOrganization($orgId)
    {
        $user = Auth::user();
        
        // Verify user is member of this organization
        $membership = OrganizationMember::where('user_id', $user->id)
            ->where('organization_id', $orgId)
            ->where('status', 'ACTIVE')
            ->first();

        if (!$membership) {
            return redirect()->back()->with('error', 'You are not a member of this organization');
        }

        session(['current_org_id' => $orgId]);
        
        return redirect()->route('organization.dashboard')
            ->with('success', 'Switched to ' . $membership->organization->name);
    }

    // Show organization creation form
    public function createView()
    {
        $user = Auth::user();

        // Check if user already has a CREATOR organization
        $hasOrganization = OrganizationMember::where('user_id', $user->id)
            ->where('role', 'CREATOR')
            ->exists();

        if ($hasOrganization) {
            return redirect()->route('organization.dashboard')
                ->with('error', 'You have already created an organization');
        }

        // Get approved organization request
        $approvedRequest = OrganizationRequest::where('user_id', $user->id)
            ->where('status', 'APPROVED')
            ->latest()
            ->first();

        if (!$approvedRequest) {
            return redirect()->route('join-us')
                ->with('error', 'You need an approved organization request to create an organization');
        }

        return view('pages.organization.create', compact('approvedRequest'));
    }

    // Create new organization
    public function create(CreateOrganizationRequest $request)
    {
        $userId = Auth::id();

        // Check if user is a CREATOR of any organization
        $isCreator = OrganizationMember::where('user_id', $userId)->where('role', 'CREATOR')->exists();
        if ($isCreator) {
            return redirect()->back()
                ->with('error', 'You are already a CREATOR of an organization.');
        }

        // Check if user has approved request
        $approvedRequest = OrganizationRequest::where('user_id', $userId)
            ->where('status', 'APPROVED')
            ->latest()
            ->first();

        if (!$approvedRequest) {
            return redirect()->route('join-us')
                ->with('error', 'You need an approved organization request first.');
        }

        DB::beginTransaction();
        try {
            // Handle banner upload
            $bannerPath = $request->file('banner_img');
            $bannerName = Str::uuid() . '_' . str_replace(' ', '_', $bannerPath->getClientOriginalName());
            $bannerPath->storeAs('public/OrganizationStorage/Banner', $bannerName);

            // Handle logo upload
            $logoPath = $request->file('logo_img');
            $logoName = Str::uuid() . '_' . str_replace(' ', '_', $logoPath->getClientOriginalName());
            $logoPath->storeAs('public/OrganizationStorage/Logo', $logoName);

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
                'status' => 'ACTIVE',
                'joined_at' => now(),
            ]);

            // Set current organization in session
            session(['current_org_id' => $organization->id]);

            DB::commit();

            return redirect()->route('organization.dashboard')
                ->with('success', 'Organization created successfully! Welcome to your dashboard.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create organization: ' . $e->getMessage());
        }
    }

    // Update organization (inline editing)
    public function update(UpdateOrganizationRequest $request, $id)
    {
        try {
            $organization = Organization::findOrFail($id);
            
            $updateData = [];

            // Add text fields to update data
            if ($request->filled('name')) $updateData['name'] = $request->name;
            if ($request->filled('org_email')) $updateData['org_email'] = $request->org_email;
            if ($request->filled('desc')) $updateData['desc'] = $request->desc;
            if ($request->filled('motto')) $updateData['motto'] = $request->motto;
            if ($request->filled('website_url')) $updateData['website_url'] = $request->website_url;
            if ($request->filled('instagram_url')) $updateData['instagram_url'] = $request->instagram_url;
            if ($request->filled('x_url')) $updateData['x_url'] = $request->x_url;
            if ($request->filled('facebook_url')) $updateData['facebook_url'] = $request->facebook_url;

            // Handle banner upload
            if ($request->hasFile('banner_img')) {
                // Delete old banner if exists
                if ($organization->banner_img) {
                    $oldBannerPath = public_path('OrganizationStorage/Banner/' . $organization->banner_img);
                    if (file_exists($oldBannerPath)) {
                        unlink($oldBannerPath);
                    }
                }
                
                $bannerFile = $request->file('banner_img');
                $bannerName = Str::uuid() . '_' . str_replace(' ', '_', $bannerFile->getClientOriginalName());
                $uploadPath = public_path('OrganizationStorage/Banner');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }
                $bannerFile->move($uploadPath, $bannerName);
                Log::info("Banner image uploaded: " . $bannerName);
                $updateData['banner_img'] = $bannerName;
            }

            // Handle logo upload
            if ($request->hasFile('logo_img')) {
                // Delete old logo if exists
                if ($organization->logo_img) {
                    $oldLogoPath = public_path('OrganizationStorage/Logo/' . $organization->logo_img);
                    if (file_exists($oldLogoPath)) {
                        unlink($oldLogoPath);
                    }
                }
                
                $logoFile = $request->file('logo_img');
                $logoName = Str::uuid() . '_' . str_replace(' ', '_', $logoFile->getClientOriginalName());
                $uploadPath = public_path('OrganizationStorage/Logo');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }
                $logoFile->move($uploadPath, $logoName);
                Log::info("Logo image uploaded: " . $logoName);
                $updateData['logo_img'] = $logoName;
            }

            // Update organization
            if (!empty($updateData)) {
                $organization->update($updateData);
            }

            // Get fresh organization data with updated images
            $organization = $organization->fresh();

            return response()->json([
                'success' => true,
                'data' => [
                    'organization' => $organization,
                    'banner_url' => $organization->banner_img ? asset('OrganizationStorage/Banner/' . $organization->banner_img) : null,
                    'logo_url' => $organization->logo_img ? asset('OrganizationStorage/Logo/' . $organization->logo_img) : null,
                ],
                'message' => 'Organization updated successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update organization: ' . $e->getMessage(),
            ], 500);
        }
    }

    // Organization profile page (for creator/manager)
    public function profile()
    {
        $user = Auth::user();
        $userOrganizations = OrganizationMember::where('user_id', $user->id)
            ->where('status', 'ACTIVE')
            ->with('organization')
            ->get()
            ->map(fn($member) => [
                'id' => $member->organization->id,
                'name' => $member->organization->name,
                'slug' => $member->organization->slug,
                'role' => $member->role,
            ]);

        $firstOrg = $userOrganizations->first();
        $currentOrgId = session('current_org_id', $firstOrg['id'] ?? null);
        $currentOrg = $userOrganizations->firstWhere('id', $currentOrgId);
        
        if (!$currentOrg) {
            return redirect()->route('organization.dashboard');
        }

        $organization = Organization::with(['creator', 'organizationMembers.user'])
            ->withCount(['quests', 'organizationMembers'])
            ->findOrFail($currentOrg['id']);

        // Get stats
        $stats = [
            'rating' => 4.8, // TODO: Implement real rating system
            'total_ratings' => 0,
            'total_quests' => $organization->quests_count,
            'total_participants' => QuestParticipant::whereHas('quest', function($q) use ($organization) {
                $q->where('org_id', $organization->id);
            })->distinct('user_id')->count(),
            'active_quests' => Quest::where('org_id', $organization->id)
                ->where('status', 'ACTIVE')
                ->count(),
        ];

        return view('pages.organization.profile', compact('organization', 'currentOrg', 'stats', 'userOrganizations'));
    }

    public function getAll()
    {
        $query = Organization::query()->where('status', 'ACTIVE');

        // Live search by name, handle, or motto
        if (request('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('handle', 'like', '%' . $search . '%')
                  ->orWhere('motto', 'like', '%' . $search . '%');
            });
        }

        // Eager load relationships and counts
        $organizations = $query->withCount('quests')
            ->withCount(['organizationMembers as members_count' => function($q) {
                $q->whereIn('role', ['CREATOR', 'MANAGER']);
            }])
            ->latest()
            ->paginate(6);

        return view('pages.organizations.index', compact('organizations'));
    }

    // Public organization view
    public function show($slug)
    {
        $organization = Organization::where('slug', $slug)
            ->with(['creator', 'organizationMembers' => function($q) {
                $q->where('status', 'ACTIVE')->with('user');
            }])
            ->withCount(['quests', 'organizationMembers'])
            ->firstOrFail();

        // Get active quests
        $quests = Quest::where('org_id', $organization->id)
            ->whereIn('status', ['ACTIVE', 'ENDED'])
            ->withCount('questParticipants')
            ->latest()
            ->take(6)
            ->get();

        // Get stats
        $stats = [
            'total_quests' => $organization->quests_count,
            'total_participants' => QuestParticipant::whereHas('quest', function($q) use ($organization) {
                $q->where('org_id', $organization->id);
            })->distinct('user_id')->count(),
            'active_quests' => Quest::where('org_id', $organization->id)
                ->where('status', 'ACTIVE')
                ->count(),
            'members_count' => $organization->organization_members_count,
        ];

        return view('pages.organization.show', compact('organization', 'quests', 'stats'));
    }
}
