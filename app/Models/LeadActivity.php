<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'user_id',
        'activity_type',
        'subject',
        'description',
        'activity_date',
        'next_follow_up_date',
        'metadata',
    ];

    protected $casts = [
        'activity_date' => 'datetime',
        'next_follow_up_date' => 'datetime',
        'metadata' => 'array',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeRecent($query, $limit = 10)
    {
        return $query->orderBy('activity_date', 'desc')->limit($limit);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('activity_type', $type);
    }
}