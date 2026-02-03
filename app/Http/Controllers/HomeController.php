<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\OrganizationMember;
use App\Models\OrganizationRequest;
use App\Models\Quest;
use App\Models\QuestParticipant;
use App\Models\QuestWinner;
use App\Models\Article;
use App\Models\User;
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
        $user = Auth::user();
        
        // Get user's quest participations with quest details
        $questParticipations = QuestParticipant::where('user_id', $user->id)
            ->with(['quest.organization'])
            ->latest()
            ->get();
        
        // Calculate stats
        $completedQuests = $questParticipations->where('status', 'COMPLETED')->count();
        $pendingQuests = $questParticipations->whereIn('status', ['JOINED', 'SUBMITTED'])->count();
        $totalPoints = $questParticipations->where('status', 'COMPLETED')->sum(function($participation) {
            return $participation->quest->reward_points ?? 0;
        });
        
        // Get user's quest wins
        $questWins = QuestWinner::where('user_id', $user->id)
            ->with(['quest.organization', 'prize'])
            ->latest()
            ->get();
        
        // Get user's articles
        $articles = Article::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();
        
        // Calculate user rank based on points (position among all users)
        $userRank = User::where('is_removed', false)
            ->whereHas('questParticipants', function($q) {
                $q->where('status', 'COMPLETED');
            })
            ->get()
            ->map(function($u) {
                return [
                    'id' => $u->id,
                    'points' => $u->questParticipants->where('status', 'COMPLETED')->sum(function($p) {
                        return $p->quest->reward_points ?? 0;
                    })
                ];
            })
            ->sortByDesc('points')
            ->values()
            ->search(function($item) use ($user) {
                return $item['id'] === $user->id;
            });
        
        $rank = $userRank !== false ? $userRank + 1 : '-';
        
        // Get user's organizations (where they're a member)
        $organizations = OrganizationMember::where('user_id', $user->id)
            ->where('status', 'ACTIVE')
            ->with('organization')
            ->get();
        
        return view('pages.profile.index', compact(
            'user',
            'questParticipations',
            'completedQuests',
            'pendingQuests',
            'totalPoints',
            'questWins',
            'articles',
            'rank',
            'organizations'
        ));
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

        // Get pending organization invitations
        $pendingInvitations = OrganizationMember::where('user_id', $user->id)
            ->where('status', 'PENDING')
            ->with('organization')
            ->get();

        return view('pages.dashboard.index', compact('approvedRequest', 'pendingInvitations'));
    }
}
