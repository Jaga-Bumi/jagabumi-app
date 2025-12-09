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
        $user = Auth::user();

        $quest = Quest::find($questId);

        if (!$quest) {
            return response()->json([
                'success' => false,
                'message' => 'Quest not found.',
            ], 404);
        }

        // Check if user is a participant
        $participant = QuestParticipant::where('quest_id', $questId)
            ->where('user_id', $user->id)
            ->first();

        if (!$participant) {
            return response()->json([
                'success' => false,
                'message' => 'You are not a participant of this quest.',
            ], 403);
        }

        // Check if already submitted
        if ($participant->status === 'COMPLETED' || $participant->status === 'APPROVED' || $participant->status === 'REJECTED') {
            return response()->json([
                'success' => false,
                'message' => 'You have already submitted proof for this quest.',
            ], 400);
        }

        // Check if quest period is active or ended
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

        try {
            // Upload video to public storage
            $video = $request->file('video');
            $videoName = time() . '_' . $video->getClientOriginalName();
            
            // Create directory if not exists
            $uploadPath = public_path('QuestSubmissionStorage');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            
            // Move file to public directory
            $video->move($uploadPath, $videoName);
            $videoUrl = '/QuestSubmissionStorage/' . $videoName;

            // Update participant record
            $participant->update([
                'video_url' => $videoUrl,
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
            ], 200);

        } catch (\Exception $e) {
            Log::error('Failed to submit proof: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit proof. Please try again. Error: ' . $e->getMessage(),
            ], 500);
        }
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

}
