<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quest extends Model
{
    use HasFactory;

    protected $table = 'quests';

    protected $fillable = [
        'title',
        'slug',
        'desc',
        'banner_url',
        'location_name',
        'latitude',
        'longitude',
        'radius_meter',
        'liveness_code',
        'registration_start_at',
        'registration_end_at',
        'quest_start_at',
        'quest_end_at',
        'judging_start_at',
        'judging_end_at',
        'prize_distribution_date',
        'status',
        'participant_limit',
        'winner_limit',
        'approval_date',
        'org_id',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'radius_meter' => 'integer',
        'participant_limit' => 'integer',
        'winner_limit' => 'integer',
        'registration_start_at' => 'datetime',
        'registration_end_at' => 'datetime',
        'quest_start_at' => 'datetime',
        'quest_end_at' => 'datetime',
        'judging_start_at' => 'datetime',
        'judging_end_at' => 'datetime',
        'prize_distribution_date' => 'datetime',
        'approval_date' => 'datetime',
    ];

    // Relationships
    public function organization()
    {
        return $this->belongsTo(Organization::class, 'org_id');
    }

    public function prizes()
    {
        return $this->hasMany(Prize::class);
    }

    public function questParticipants()
    {
        return $this->hasMany(QuestParticipant::class);
    }

    public function questWinners()
    {
        return $this->hasMany(QuestWinner::class);
    }

    public function participants()
    {
        return $this->belongsToMany(User::class, 'quest_participant')
            ->withPivot('joined_at', 'status', 'video_url', 'description', 'submission_date')
            ->withTimestamps();
    }

    public function winners()
    {
        return $this->belongsToMany(User::class, 'quest_winners')
            ->withPivot('reward_distributed', 'tx_hash', 'distributed_at')
            ->withTimestamps();
    }

    // Accessors
    public function getBannerUrlAttribute($value)
    {
        if (!$value) {
            return null;
        }
        // If already a full URL, return as is
        if (str_starts_with($value, 'http') || str_starts_with($value, '/')) {
            return $value;
        }
        // Otherwise, prepend the storage path
        return asset('storage/QuestStorage/Banner/' . $value);
    }
}
