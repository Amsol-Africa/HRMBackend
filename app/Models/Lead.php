<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'contact_submission_id',
        'campaign_id',
        'name',
        'email',
        'phone',
        'status',
        'label',
        'source'
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function contactSubmission()
    {
        return $this->belongsTo(ContactSubmission::class);
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function activities()
    {
        return $this->hasMany(LeadActivity::class);
    }
}
