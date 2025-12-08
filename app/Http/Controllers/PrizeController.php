<?php

namespace App\Http\Controllers;

use App\Models\Prize;
use App\Models\PrizeUser;
use Illuminate\Http\Request;

class PrizeController extends Controller
{
    //
    public function myPrizes($id){

        $prizes = PrizeUser::with('prize')->where('user_id', $id)->get();

        return response()->json([
            'prizes' => $prizes
        ]);

    }
}
