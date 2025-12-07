<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $table = 'articles';

    protected $fillable = [
        'slug',
        'title',
        'body',
        'thumbnail',
        'is_deleted',
        'org_id',
        'user_id',
    ];

    protected $casts = [
        'is_deleted' => 'boolean',
    ];

    // Relationships
    public function organization()
    {
        return $this->belongsTo(Organization::class, 'org_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
