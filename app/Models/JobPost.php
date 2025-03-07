<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\ModelStatus\HasStatuses;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JobPost extends Model
{
    use HasFactory, HasStatuses, HasSlug, LogsActivity;
    protected $fillable = [
        'business_id',
        'location_id',
        'department_id',
        'title',
        'slug',
        'description',
        'salary_range',
        'employment_type',
        'place',
        'created_by',
        'closed_at',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('title')->saveSlugsTo('slug');
    }

    protected $casts = [
        'closed_at' => 'datetime',
    ];

    // Relationship with Business
    public function business()
    {
        return $this->belongsTo(Business::class);
    }
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    // Relationship with User (who created the job)
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relationship with Job Applications
    public function applications()
    {
        return $this->hasMany(Application::class);
    }

}
