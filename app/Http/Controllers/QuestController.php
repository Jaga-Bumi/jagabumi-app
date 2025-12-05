<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuestRequest;
use App\Models\Quest;
use Illuminate\Http\Request;

class QuestController extends Controller
{
    // Read all / one
    public function readAll(){

        $quests = Quest::all();

        dd($quests);
    }

    public function readOne($id){
        $quest = Quest::find($id);

        dd($quest);
    }

    // Create quest
    public function create(QuestRequest $request){

    }

}
