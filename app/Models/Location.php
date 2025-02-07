<?php

namespace App\Models;

use Spatie\ModelStatus\HasStatuses;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Location extends Model
{
    use HasFactory, HasSlug, HasStatuses;

    protected $fillable = [
        'business_id',
        'name',
        'slug',
        'company_size',
        'registration_no',
        'tax_pin_no',
        'business_license_no',
        'physical_address',
    ];
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }
    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('name')->saveSlugsTo('slug');
    }
}
