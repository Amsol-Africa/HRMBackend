<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NotificationPreference extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'user_id',
        'email',
        'database',
        'sms',
        'slack',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
