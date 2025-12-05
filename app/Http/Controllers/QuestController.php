<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuestRequest;
use App\Models\Quest;
use Illuminate\Http\Request;

class QuestController extends Controller
{
    // Read all / one
    public function readAll(){

        // $quests = Quest::all();

        // dd($quests);
        
        return view('pages.quests.index');
    }

    public function readOne($id){
        // $quest = Quest::find($id);

        // dd($quest);

        return view('pages.quests.show', compact('id'));
    }

    // Create quest
    public function create(QuestRequest $request){

    }

}
