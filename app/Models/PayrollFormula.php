<?php

namespace App\Models;

use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PayrollFormula extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'business_id',
        'name',
        'formula_type',
        'slug',
        'calculation_basis',
        'is_progressive',
        'minimum_amount',
    ];

    public function brackets()
    {
        return $this->hasMany(PayrollFormulaBracket::class);
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('name')->saveSlugsTo('slug');
    }
}
