<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quest extends Model
{
    //
    protected $guarded = [];

    public function organizations(){
        return $this->belongsTo(Organization::class);
    }

    public function members(){
        return $this->belongsToMany(Member::class);
    }

    public function prizes(){
        return $this->hasMany(Prize::class);
    }
}
