<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prize extends Model
{
    use HasFactory;

    protected $table = 'prizes';

    protected $fillable = [
        'name',
        'type',
        'image_url',
        'description',
        'quest_id',
    ];

    // Relationships
    public function quest()
    {
        return $this->belongsTo(Quest::class);
    }

    public function prizeUsers()
    {
        return $this->hasMany(PrizeUser::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'prize_users')
            ->withPivot('nft_token_id', 'tx_hash', 'token_uri')
            ->withTimestamps();
    }
}
