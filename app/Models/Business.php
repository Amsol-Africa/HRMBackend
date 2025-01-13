<?php

namespace App\Models;

use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Business extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'user_id',
        'company_name',
        'slug',
        'industry',
        'company_size',
        'country',
        'address',
        'tax_id',
        'website',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('company_name')->saveSlugsTo('slug');
    }
}
