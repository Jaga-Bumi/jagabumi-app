<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuestParticipant\SubmitProofRequest;
use App\Http\Requests\QuestParticipant\ReviewSubmissionRequest;
use App\Models\OrganizationMember;
use App\Models\Quest;
use App\Models\QuestParticipant;
use App\Models\QuestWinner;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage; // Added Storage Facade

class QuestParticipantController extends Controller
{
    // Join a quest
    public function join($questId)
    {
        $user = Auth::user();
        $quest = Quest::findOrFail($questId);

        // Check if user is a member of the quest's organization
        $isMember = OrganizationMember::where('organization_id', $quest->org_id)
            ->where('user_id', $user->id)
            ->where('status', 'ACTIVE')
            ->exists();

        if ($isMember) {
            return response()->json([
                'success' => false,
                'message' => 'Organization members cannot join their own quest.',
            ], 403);
        }

        // Check if registration is open
        if (now()->lt($quest->registration_start_at)) {
            return response()->json([
                'success' => false,
                'message' => 'Registration has not started yet. Opens at: ' . $quest->registration_start_at->format('M d, Y H:i'),
            ], 400);
        }

        if (now()->gt($quest->registration_end_at)) {
            return response()->json([
                'success' => false,
                'message' => 'Registration has ended. Closed at: ' . $quest->registration_end_at->format('M d, Y H:i'),
            ], 400);
        }

        // Check if user already joined
        if (QuestParticipant::where('quest_id', $questId)->where('user_id', $user->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'You have already joined this quest.',
            ], 400);
        }

        // Check participant limit
        if (QuestParticipant::where('quest_id', $questId)->count() >= $quest->participant_limit) {
            return response()->json([
                'success' => false,
                'message' => 'This quest has reached its participant limit (' . $quest->participant_limit . ').',
            ], 400);
        }

        // Create participation
        $participation = QuestParticipant::create([
            'quest_id' => $questId,
            'user_id' => $user->id,
            'joined_at' => now(),
            'status' => 'REGISTERED',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Successfully joined the quest!',
            'data' => [
                'participation_id' => $participation->id,
                'quest_title' => $quest->title,
                'status' => $participation->status,
                'joined_at' => $participation->joined_at,
            ],
        ], 201);
    }

    // Cancel participation
    public function cancelParticipation($questId)
    {
        $user = Auth::user();
        $quest = Quest::findOrFail($questId);

        $participation = QuestParticipant::where('quest_id', $questId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        if ($participation->status !== 'REGISTERED') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot cancel participation. Current status: ' . $participation->status,
            ], 400);
        }

        if (now()->gt($quest->registration_end_at)) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot cancel participation. Registration period has ended.',
            ], 400);
        }

        $participation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Successfully cancelled your participation.',
        ]);
    }
    

    // Submit proof for quest participation
    public function submitProof(SubmitProofRequest $request, $questId)
    {
        $user = Auth::user();
        $quest = Quest::findOrFail($questId);

        $participant = QuestParticipant::where('quest_id', $questId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        if (in_array($participant->status, ['COMPLETED', 'APPROVED', 'REJECTED'])) {
            return response()->json([
                'success' => false,
                'message' => 'You have already submitted proof for this quest.',
            ], 400);
        }

        if (now()->lt($quest->quest_start_at)) {
            return response()->json([
                'success' => false,
                'message' => 'Quest has not started yet. Starts at: ' . $quest->quest_start_at->format('M d, Y H:i'),
            ], 400);
        }

        if (now()->gt($quest->judging_end_at)) {
            return response()->json([
                'success' => false,
                'message' => 'Submission period has ended. Deadline was: ' . $quest->judging_end_at->format('M d, Y H:i'),
            ], 400);
        }

        // --- UPDATED: Upload video using Storage Facade ---
        $video = $request->file('video');
        $videoName = time() . '_' . $video->getClientOriginalName();
        
        // Store directly to storage/app/public/QuestSubmissionStorage
        $video->storeAs('QuestSubmissionStorage', $videoName, 'public');
        
        // Update model with the relative path (asset helper will handle the 'storage/' prefix)
        // Or store full relative path 'QuestSubmissionStorage/video.mp4' if your accessor handles it
        // Assuming your previous code stored '/QuestSubmissionStorage/video.mp4', we'll keep it consistent relative to storage root
        $videoUrl = $videoName; // Just the filename if you construct path in accessor, or 'QuestSubmissionStorage/' . $videoName

        $participant->update([
            'video_url' => $videoUrl, // Make sure your frontend/accessor handles this (e.g. asset('storage/QuestSubmissionStorage/' . $videoUrl))
            'description' => $request->description,
            'submission_date' => now(),
            'status' => 'COMPLETED',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Proof submitted successfully! Your submission is now under review.',
            'data' => [
                'video_url' => $videoUrl,
                'submission_date' => $participant->submission_date->format('M d, Y H:i'),
            ],
        ]);
    }

    
    // View my quest participations
    public function myParticipations()
    {
        $user = Auth::user();

        $participations = QuestParticipant::where('user_id', $user->id)
            ->with('quest:id,title,slug,status,banner_url,quest_start_at,quest_end_at')
            ->orderBy('joined_at', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $participations,
        ], 200);
    }

    
    // View all submissions untuk quest
    public function submissions($questId)
    {
        $quest = Quest::find($questId);

        if (!$quest) {
            return response()->json([
                'success' => false,
                'message' => 'Quest not found.',
            ], 404);
        }

        $submissions = QuestParticipant::where('quest_id', $questId)
            ->whereIn('status', ['COMPLETED', 'APPROVED', 'REJECTED'])
            ->with('user:id,name,avatar_url,wallet_address')
            ->orderBy('submission_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $submissions,
        ], 200);
    }

    // View detail submission
    public function viewSubmission($participantId)
    {
        $participant = QuestParticipant::with([
            'user:id,name,avatar_url,wallet_address,email',
            'quest:id,title,slug,status'
        ])->find($participantId);

        if (!$participant) {
            return response()->json([
                'success' => false,
                'message' => 'Submission not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $participant,
        ], 200);
    }

    // Review submission
    public function reviewSubmission(ReviewSubmissionRequest $request, $participantId)
    {
        $participant = QuestParticipant::find($participantId);

        if (!$participant) {
            return response()->json([
                'success' => false,
                'message' => 'Submission not found.',
            ], 404);
        }

        if ($participant->status !== 'COMPLETED') {
            return response()->json([
                'success' => false,
                'message' => 'Only COMPLETED submissions can be reviewed.',
            ], 400);
        }

        try {
            $participant->update([
                'status' => $request->status, // APPROVED or REJECTED
                'admin_notes' => $request->admin_notes,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Submission reviewed successfully.',
                'data' => $participant,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Failed to review submission: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to review submission. Please try again.',
            ], 500);
        }
    }

    // Approve submission (for organization)
    public function approveSubmission($submissionId)
    {
        $submission = QuestParticipant::with('quest')->findOrFail($submissionId);
        $this->authorizeOrganizationMember($submission->quest->org_id);

        if ($submission->status !== 'COMPLETED') {
            return response()->json([
                'success' => false,
                'message' => 'Only completed submissions can be approved',
            ], 400);
        }

        $quest = $submission->quest;

        DB::beginTransaction();
        try {
            // Update submission status
            $submission->update([
                'status' => 'APPROVED',
            ]);

            // Add to QuestWinner table
            QuestWinner::create([
                'quest_id' => $quest->id,
                'user_id' => $submission->user_id,
                'reward_distributed' => false,
            ]);

            DB::commit();

            $currentWinnersCount = QuestWinner::where('quest_id', $quest->id)->count();

            return response()->json([
                'success' => true,
                'message' => 'Approved! (' . $currentWinnersCount . '/' . $quest->winner_limit . ' winners)',
                'winners_count' => $currentWinnersCount,
                'winner_limit' => $quest->winner_limit,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to approve submission: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve submission. Please try again.',
            ], 500);
        }
    }

    // Reject/Unapprove submission (for organization)
    public function rejectSubmission($submissionId)
    {
        $submission = QuestParticipant::with('quest')->findOrFail($submissionId);
        $this->authorizeOrganizationMember($submission->quest->org_id);

        $quest = $submission->quest;
        
        // Handle unapproving a winner - return to pending (COMPLETED)
        if ($submission->status === 'APPROVED') {
            DB::beginTransaction();
            try {
                // Remove from QuestWinner table
                QuestWinner::where('quest_id', $quest->id)
                    ->where('user_id', $submission->user_id)
                    ->delete();
                
                // Return to pending status
                $submission->update([
                    'status' => 'COMPLETED',
                ]);
                
                DB::commit();
                
                $currentWinnersCount = QuestWinner::where('quest_id', $quest->id)->count();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Removed from winners. Returned to pending.',
                    'winners_count' => $currentWinnersCount,
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Failed to unapprove submission: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to remove from winners. Please try again.',
                ], 500);
            }
        }

        // Standard reject for COMPLETED submissions
        if ($submission->status !== 'COMPLETED') {
            return response()->json([
                'success' => false,
                'message' => 'Invalid submission status',
            ], 400);
        }

        $submission->update([
            'status' => 'REJECTED',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Submission rejected',
        ]);
    }

    private function authorizeOrganizationMember($orgId)
    {
        $membership = \App\Models\OrganizationMember::where('user_id', Auth::id())
            ->where('organization_id', $orgId)
            ->where('status', 'ACTIVE')
            ->first();

        if (!$membership) {
            abort(403, 'Unauthorized');
        }
    }

    // Get all submissions for organization's quests
    public function organizationSubmissions()
    {
        $user = Auth::user();
        
        // Get user's organizations for the sidebar
        $userOrganizations = OrganizationMember::where('user_id', $user->id)
            ->where('status', 'ACTIVE')
            ->with('organization')
            ->get()
            ->map(fn($member) => [
                'id' => $member->organization->id,
                'name' => $member->organization->name,
                'handle' => $member->organization->handle,
                'logo_img' => $member->organization->logo_img,
                'role' => $member->role,
            ]);

        $firstOrg = $userOrganizations->first();
        $orgId = session('current_org_id', $firstOrg['id'] ?? null);
        $currentOrg = $userOrganizations->firstWhere('id', $orgId);
        
        if (!$currentOrg) {
            return redirect()->route('organization.dashboard')
                ->with('error', 'Please select or create an organization first.');
        }

        // Get submissions with quest details including winner_limit
        $submissions = QuestParticipant::whereHas('quest', function($q) use ($orgId) {
                $q->where('org_id', $orgId);
            })
            ->whereIn('status', ['COMPLETED', 'APPROVED', 'REJECTED'])
            ->with(['user:id,name,avatar_url,wallet_address,email', 'quest:id,title,slug,banner_url,winner_limit'])
            ->orderBy('submission_date', 'desc')
            ->get();

        // Get quests with winner stats for the filter and progress display
        $quests = Quest::where('org_id', $orgId)
            ->whereIn('status', ['ACTIVE', 'ENDED'])
            ->withCount(['winners'])
            ->get()
            ->map(function($quest) {
                // Keep only needed fields and ensure winners_count is available
                return [
                    'id' => $quest->id,
                    'title' => $quest->title,
                    'slug' => $quest->slug,
                    'winner_limit' => $quest->winner_limit,
                    'status' => $quest->status,
                    'winners_count' => $quest->winners_count ?? 0,
                ];
            });

        return view('pages.organization.submissions', compact('submissions', 'userOrganizations', 'currentOrg', 'quests'));
    }
}