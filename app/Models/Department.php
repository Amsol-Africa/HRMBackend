<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\ModelStatus\HasStatuses;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Department extends Model
{
    use HasFactory, HasSlug, HasStatuses;

    protected $fillable = [
        'business_id',
        'name',
        'slug',
        'description',
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
