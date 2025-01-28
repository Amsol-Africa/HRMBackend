<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\ModelStatus\HasStatuses;

class AccessRequest extends Model
{
    use HasFactory,HasStatuses;

    protected $fillable = [
        'requester_id',
        'business_id',
        'email',
        'registration_token'
    ];
    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }
    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }
    public function targetUser()
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }
}
