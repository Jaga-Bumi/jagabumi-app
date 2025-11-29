<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    //
    protected $guarded = [];

    public function organizations(){
        return $this->belongsTo(Organization::class);
    }

    public function members(){
        return $this->belongsTo(Member::class);
    }
}
