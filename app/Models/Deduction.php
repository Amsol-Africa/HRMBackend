<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\ModelStatus\HasStatuses;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Deduction extends Model
{
    use HasFactory, HasStatuses, HasSlug, LogsActivity;

    protected $fillable = [
        'business_id',
        'location_id',
        'name',
        'slug',
        'description',
        'created_by',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('name')->saveSlugsTo('slug');
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function employeeDeductions()
    {
        return $this->hasMany(EmployeeDeduction::class);
    }
}
