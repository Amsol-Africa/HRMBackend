<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'campaign_id',
        'contact_submission_id',
        'user_id',
        'name',
        'email',
        'phone',
        'country',
        'message',
        'source',
        'status',
        'label',
        'survey_responses',
    ];

    protected $casts = [
        'survey_responses' => 'array',
        'status' => 'string',
        'label' => 'string',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function contactSubmission()
    {
        return $this->belongsTo(ContactSubmission::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function activities()
    {
        return $this->hasMany(LeadActivity::class);
    }
}
