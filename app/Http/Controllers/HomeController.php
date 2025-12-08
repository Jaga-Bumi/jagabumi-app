<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\Quest;

class HomeController extends Controller
{
    public function ewfjewfjb() {
        $top3Quests = Quest::where('status', 'ACTIVE')
            ->with('organization')
            ->withCount('questParticipants')
            ->latest()
            ->take(3)
            ->get();
        $top3Orgs = Organization::where('status', 'ACTIVE')->latest()->take(3)->get();
        return view('pages.tests.home', compact('top3Quests', 'top3Orgs'));
    }
}
