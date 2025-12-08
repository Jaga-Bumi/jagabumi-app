<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganizationRequest extends Model
{
    use HasFactory;

    protected $table = 'organization_requests';

    protected $fillable = [
        'user_id',
        'organization_name',
        'organization_description',
        'organization_type',
        'website_url',
        'instagram_url',
        'x_url',
        'facebook_url',
        'reason',
        'planned_activities',
        'admin_notes',
        'status',
        'approved_by',
        'responded_at',
    ];

    protected $casts = [
        'responded_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
