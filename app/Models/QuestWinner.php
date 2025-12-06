<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestWinner extends Model
{
    use HasFactory;

    protected $table = 'quest_winners';

    protected $fillable = [
        'quest_id',
        'user_id',
        'reward_distributed',
        'tx_hash',
        'distributed_at',
    ];

    protected $casts = [
        'reward_distributed' => 'boolean',
        'distributed_at' => 'datetime',
    ];

    // Relationships
    public function quest()
    {
        return $this->belongsTo(Quest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
