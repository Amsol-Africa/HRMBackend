<?php

namespace App\Models;

use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\ModelStatus\HasStatuses;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JobCategory extends Model
{
    use HasFactory, HasSlug ,HasStatuses;

    protected $fillable = [
        'business_id',
        'name',
        'slug',
        'description',
        'is_active',
    ];
    public function business()
    {
        return $this->belongsTo(Business::class);
    }
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('name')->saveSlugsTo('slug');
    }
}
