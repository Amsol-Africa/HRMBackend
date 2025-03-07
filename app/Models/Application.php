<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\ModelStatus\HasStatuses;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Application extends Model implements HasMedia {

    use HasFactory, HasStatuses, InteractsWithMedia, LogsActivity;

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
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('applications');
    }
    public function getImageUrl()
    {
        $media = $this->getFirstMedia('applications');
        if ($media && File::exists($media->getPath())) {
            return $media->getUrl();
        }
        return asset('media/avatar.png');
    }
}
