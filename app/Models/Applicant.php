<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\ModelStatus\HasStatuses;

class Applicant extends Model
{
    use HasFactory, HasStatuses;

    protected $fillable = [
        'user_id',
        'address',
        'city',
        'state',
        'zip_code',
        'country',
        'linkedin_profile',
        'portfolio_url',
        'summary',
        'current_job_title',
        'current_company',
        'experience_level',
        'education_level',
        'desired_salary',
        'job_preferences',
        'source',
        'created_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function applications()
    {
        return $this->hasMany(Application::class);
    }
    public function skills()
{
    return $this->belongsToMany(Skill::class)->withPivot('skill_level');
}
}
