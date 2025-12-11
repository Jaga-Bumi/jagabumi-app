<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'handle',
        'email',
        'bio',
        'phone',
        'verifier_id',
        'wallet_address',
        'avatar_url',
        'auth_provider',
        'password',
        'role',
        'is_removed',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relationships
    public function createdOrganizations()
    {
        return $this->hasMany(Organization::class, 'created_by');
    }

    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    public function questParticipants()
    {
        return $this->hasMany(QuestParticipant::class);
    }

    public function prizeUsers()
    {
        return $this->hasMany(PrizeUser::class);
    }

    public function questWinners()
    {
        return $this->hasMany(QuestWinner::class);
    }

    public function organizationRequests()
    {
        return $this->hasMany(OrganizationRequest::class);
    }

    public function approvedRequests()
    {
        return $this->hasMany(OrganizationRequest::class, 'approved_by');
    }

    public function organizationMembers()
    {
        return $this->hasMany(OrganizationMember::class);
    }

    public function organizations()
    {
        return $this->belongsToMany(Organization::class, 'organization_members')
            ->withPivot('role', 'status', 'joined_at')
            ->withTimestamps();
    }

    // Get the organization where user is CREATOR
    public function createdOrganization()
    {
        return $this->hasOne(OrganizationMember::class)
            ->where('role', 'CREATOR')
            ->with('organization');
    }
}
