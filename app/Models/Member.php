<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    //
    protected $guarded = [];

    public function article(){
        return $this->hasMany(Article::class);
    }

    public function quests(){
        return $this->belongsToMany(Quest::class);
    }

    public function prizes(){
        return $this->belongsToMany(Prize::class);
    }
}
