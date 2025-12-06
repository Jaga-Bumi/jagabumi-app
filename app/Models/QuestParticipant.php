<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestParticipant extends Model
{
    use HasFactory;

    protected $table = 'quest_participant';

    protected $fillable = [
        'joined_at',
        'status',
        'video_url',
        'description',
        'submission_date',
        'quest_id',
        'user_id',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'submission_date' => 'datetime',
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

    public function questAttendances()
    {
        return $this->hasMany(QuestAttendance::class);
    }
}
