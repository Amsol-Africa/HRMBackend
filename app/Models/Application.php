<?php

namespace App\Models;

use Spatie\ModelStatus\HasStatuses;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Application extends Model
{
    use HasFactory, HasStatuses;

    protected $fillable = [
        'business_id',
        'location_id',
        'applicant_id',
        'job_post_id',
        'cover_letter',
        'stage',
        'notes',   
        'created_by',
        'match_score',
    ];

    protected $casts = [
         'applied_at' => 'datetime'
    ];

    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }

    public function jobPost()
    {
        return $this->belongsTo(JobPost::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function interviews()
    {
        return $this->hasMany(Interview::class);
    }
    //applicatnt skill level
    public function skills()
    {
        return $this->belongsToMany(Skill::class)->withPivot('skill_level');
    }

    public function scopeForBusiness($query, $businessId)
    {
        return $query->where('business_id', $businessId);
    }
}
