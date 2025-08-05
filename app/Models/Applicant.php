<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Spatie\ModelStatus\HasStatuses;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Applicant extends Model
{
    use HasFactory, HasStatuses, LogsActivity;

    protected $fillable = [
        'user_id',
        'fullname',
        'idnumber',
        'phone',
        'whatsapp_no',
        'age',
        'address',
        'city',
        'state',
        'zip_code',
        'country',
        'specialization',
        'academic_level',
        'company1',
        'company2',
        'company3',
        'salary_expectation',
        'linkedin_profile',
        'portfolio_url',
        'summary',
        'current_job_title',
        'current_company',
        'experience_level',
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
        return $this->belongsToMany(Skill::class, 'applicant_skills')
            ->withPivot('skill_level');
    }
}
