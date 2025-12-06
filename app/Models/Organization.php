<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;

    protected $table = 'organizations';

    protected $fillable = [
        'created_by',
        'name',
        'slug',
        'handle',
        'org_email',
        'desc',
        'motto',
        'banner_img',
        'logo_img',
        'is_verified',
        'website_url',
        'instagram_url',
        'x_url',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
    ];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function quests()
    {
        return $this->hasMany(Quest::class, 'org_id');
    }

    public function articles()
    {
        return $this->hasMany(Article::class, 'org_id');
    }

    public function organizationMembers()
    {
        return $this->hasMany(OrganizationMember::class);
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'organization_members')
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }
}
