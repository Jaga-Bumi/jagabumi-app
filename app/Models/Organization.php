<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    //
    protected $guarded = [];

    public function quests(){
        return $this->hasMany(Quest::class);
    }

    public function articles(){
        return $this->hasMany(Article::class);
    }

    public function users(){
        return $this->hasMany(User::class);
    }
}
