<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Organization;
use App\Models\Quest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeaderboardController extends Controller
{
    public function index()
    {
        // Top 10 users with most completed quests
        $topUsers = User::select('users.id', 'users.name', 'users.handle', 'users.avatar_url')
            ->join('quest_participant', 'users.id', '=', 'quest_participant.user_id')
            ->where('quest_participant.status', 'COMPLETED')
            ->groupBy('users.id', 'users.name', 'users.handle', 'users.avatar_url')
            ->selectRaw('COUNT(quest_participant.id) as completed_quests_count')
            ->orderByDesc('completed_quests_count')
            ->take(10)
            ->get();

        // Top 10 organizations with most quests created
        $topOrganizations = Organization::select('organizations.id', 'organizations.name', 'organizations.handle', 'organizations.logo_img')
            ->withCount('quests')
            ->orderByDesc('quests_count')
            ->take(10)
            ->get();

        // Top 10 most participated quests (regardless of status)
        $topQuests = Quest::select('quests.id', 'quests.title', 'quests.slug', 'quests.status', 'quests.org_id')
            ->with('organization:id,name,handle')
            ->withCount('questParticipants')
            ->orderByDesc('quest_participants_count')
            ->take(10)
            ->get();

        return view('pages.tests.leaderboard', compact('topUsers', 'topOrganizations', 'topQuests'));
    }
}
