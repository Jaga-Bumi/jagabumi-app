<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrizeUser extends Model
{
    use HasFactory;

    protected $table = 'prize_users';

    protected $fillable = [
        'nft_token_id',
        'tx_hash',
        'token_uri',
        'prize_id',
        'user_id',
    ];

    // Relationships
    public function prize()
    {
        return $this->belongsTo(Prize::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
