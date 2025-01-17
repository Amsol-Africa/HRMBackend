<?php

namespace App\Models;

use Spatie\Sluggable\HasSlug;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Sluggable\SlugOptions;
use Spatie\ModelStatus\HasStatuses;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Business extends Model implements HasMedia
{
    use HasFactory, HasSlug, HasStatuses, InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'company_name',
        'slug',
        'industry',
        'company_size',
        'phone',
        'country',
        'code',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('company_name')->saveSlugsTo('slug');
    }
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('businesses');
    }
    public function getImageUrl()
    {
        $media = $this->getFirstMedia('businesses');
        if ($media && File::exists($media->getPath())) {
            return $media->getUrl();
        }
        return asset('media/amsol-logo.png');
    }



    public function modules()
    {
        return $this->belongsToMany(Module::class, 'business_modules')->withPivot('is_active', 'subscription_ends_at')->withTimestamps();
    }
    public function activeModules()
    {
        return $this->modules()->wherePivot('is_active', true);
    }
    public function coreModules()
    {
        return $this->modules()->where('is_core', true);
    }


    // business
    public function departments()
    {
        return $this->hasMany(Department::class);
    }
    public function job_categories()
    {
        return $this->hasMany(JobCategory::class);
    }
    public function shifts()
    {
        return $this->hasMany(Shift::class);
    }
    public function payrollFormulas()
    {
        return $this->hasMany(PayrollFormula::class);
    }

    // employees
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

}
