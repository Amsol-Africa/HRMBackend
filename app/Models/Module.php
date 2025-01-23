<?php

namespace App\Models;

use Spatie\ModelStatus\HasStatuses;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Module extends Model
{
    use HasFactory, HasSlug ,HasStatuses;
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price_monthly',
        'price_yearly',
        'is_core',
        'features',
        'icon'
    ];

    protected $casts = [
        'features' => 'array',
        'is_core' => 'boolean'
    ];
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('name')->saveSlugsTo('slug');
    }

    public function businesses()
    {
        return $this->belongsToMany(Business::class, 'business_modules')->withPivot('is_active', 'subscription_ends_at')->withTimestamps();
    }
    public function activeBusinesses()
    {
        return $this->businesses()->wherePivot('is_active', true);
    }
}
