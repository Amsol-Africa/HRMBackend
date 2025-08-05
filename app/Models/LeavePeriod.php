<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\ModelStatus\HasStatuses;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeavePeriod extends Model
{
    use HasFactory, HasStatuses, HasSlug, LogsActivity;
    protected $fillable = [
        'business_id',
        'name',
        'slug',
        'start_date',
        'end_date',
        'is_active',
        'accept_applications',
        'can_accrue',
        'restrict_applications_within_dates',
        'archive',
        'autocreate',
    ];
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'accept_applications' => 'boolean',
        'can_accrue' => 'boolean',
        'restrict_applications_within_dates' => 'boolean',
        'archive' => 'boolean',
        'autocreate' => 'boolean',
    ];
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('name')->saveSlugsTo('slug');
    }
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

}
