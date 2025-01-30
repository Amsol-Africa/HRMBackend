<?php

namespace App\Models;

use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Relief extends Model
{
    use HasFactory, HasSlug;
    protected $fillable = [
        'name',
        'slug',
        'tax_application',
        'relief_type',
        'comparison_method',
        'rate_percentage',
        'fixed_amount',
        'maximum_relief',
        'is_mandatory',
        'business_id',
    ];

    protected $casts = [
        'rate_percentage' => 'float',
        'fixed_amount'    => 'float',
        'maximum_relief'  => 'float',
        'is_mandatory'    => 'boolean',
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
