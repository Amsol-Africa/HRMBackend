<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportIssue extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'business_id',
        'title',
        'description',
        'screenshot_path',
        'status',
        'solved_by_id',
        'solved_at',
    ];

    protected $casts = [
        'solved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function solvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'solved_by_id');
    }
}