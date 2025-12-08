<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuestParticipant\SubmitProofRequest;
use App\Http\Requests\QuestParticipant\ReviewSubmissionRequest;
use App\Models\Quest;
use App\Models\QuestParticipant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class QuestParticipantController extends Controller
{
    // Join a quest
    public function join($questId)
    {
        $user = Auth::user();

        $quest = Quest::find($questId);

        if (!$quest) {
            return response()->json([
                'success' => false,
                'message' => 'Quest not found.',
            ], 404);
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
        $existingParticipation = QuestParticipant::where('quest_id', $questId)
            ->where('user_id', $user->id)
            ->first();

        if ($existingParticipation) {
            return response()->json([
                'success' => false,
                'message' => 'You have already joined this quest. Status: ' . $existingParticipation->status,
            ], 400);
        }

        // Check participant limit
        $currentParticipants = QuestParticipant::where('quest_id', $questId)->count();

        if ($currentParticipants >= $quest->participant_limit) {
            return response()->json([
                'success' => false,
                'message' => 'This quest has reached its participant limit (' . $quest->participant_limit . ').',
            ], 400);
        }

        // Create participation
        try {
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
        } catch (\Exception $e) {
            Log::error('Failed to join quest: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to join quest. Please try again.',
            ], 500);
        }
    }

    // Cancel participation
    public function cancelParticipation($questId)
    {
        $user = Auth::user();

        $quest = Quest::find($questId);

        if (!$quest) {
            return response()->json([
                'success' => false,
                'message' => 'Quest not found.',
            ], 404);
        }

        // Find participation
        $participation = QuestParticipant::where('quest_id', $questId)
            ->where('user_id', $user->id)
            ->first();

        if (!$participation) {
            return response()->json([
                'success' => false,
                'message' => 'You are not registered for this quest.',
            ], 404);
        }

        if ($participation->status !== 'REGISTERED') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot cancel participation. Current status: ' . $participation->status,
            ], 400);
        }

        // Check if cancellation is still allowed (during registration period)
        if (now()->gt($quest->registration_end_at)) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot cancel participation. Registration period has ended.',
            ], 400);
        }

        try {
            $participation->delete();

            return response()->json([
                'success' => true,
                'message' => 'Successfully cancelled your participation.',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to cancel participation: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel participation. Please try again.',
            ], 500);
        }
    }
    

    // Submit proof for quest participation

    // update status ke COMPLETED
    public function submitProof(SubmitProofRequest $request, $questId)
    {
    }

    
    // View my quest participations
    public function myParticipations()
    {
    }

    
    // View all submissions untuk quest
    public function submissions($questId)
    {
    }

    // View detail submission
    public function viewSubmission($participantId)
    {
    }

    // Review submission
    public function reviewSubmission(ReviewSubmissionRequest $request, $participantId)
    {

    }

}
