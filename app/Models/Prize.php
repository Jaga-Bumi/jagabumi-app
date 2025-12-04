<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prize extends Model
{
    //
    protected $guarded = [];

    public function quests(){
        return $this->belongsTo(Quest::class);
    }

    public function users(){
        return $this->belongsToMany(User::class);
    }
}
