<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\OrganizationRequest;
use App\Models\Quest;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index() {
        $top3Quests = Quest::where('status', 'ACTIVE')
            ->with('organization')
            ->withCount('questParticipants')
            ->latest()
            ->take(3)
            ->get();
        $top3Orgs = Organization::where('status', 'ACTIVE')->latest()->take(3)->get();
        return view('pages.home.index', compact('top3Quests', 'top3Orgs'));
    }

    public function profile()
    {
        return view('pages.profile.index');
    }

    public function dashboard()
    {
        $user = Auth::user();
        
        // Check if user has approved organization request
        $approvedRequest = null;
        if (!$user->createdOrganization) {
            $approvedRequest = OrganizationRequest::where('user_id', $user->id)
                ->where('status', 'APPROVED')
                ->latest()
                ->first();
        }

        return view('pages.dashboard.index', compact('approvedRequest'));
    }
}
