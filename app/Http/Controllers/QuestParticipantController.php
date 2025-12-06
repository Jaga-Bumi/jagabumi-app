<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuestParticipant\JoinQuestRequest;
use App\Http\Requests\QuestParticipant\SubmitProofRequest;
use App\Http\Requests\QuestParticipant\ReviewSubmissionRequest;
use App\Models\Quest;
use App\Models\QuestParticipant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuestParticipantController extends Controller
{
    // Join a quest
    public function join(JoinQuestRequest $request, $questId)
    {
    }

    // Cancel participation
    public function cancelParticipation($questId)
    {
    }
    

    // Submit proof for quest participation
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
