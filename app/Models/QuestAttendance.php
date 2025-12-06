<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestAttendance extends Model
{
    use HasFactory;

    protected $table = 'quest_attendances';

    protected $fillable = [
        'quest_participant_id',
        'type',
        'proof_latitude',
        'proof_longitude',
        'proof_photo_url',
        'notes',
        'distance_from_quest_location',
        'is_valid_location',
    ];

    protected $casts = [
        'proof_latitude' => 'decimal:8',
        'proof_longitude' => 'decimal:8',
        'distance_from_quest_location' => 'decimal:2',
        'is_valid_location' => 'boolean',
    ];

    // Relationships
    public function questParticipant()
    {
        return $this->belongsTo(QuestParticipant::class);
    }
}
