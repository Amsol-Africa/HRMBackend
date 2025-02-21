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
        'registration_no',
        'tax_pin_no',
        'business_license_no',
        'physical_address',
        'currency',
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
        $this->addMediaCollection('registration_certificates');
        $this->addMediaCollection('tax_pin_certificates');
        $this->addMediaCollection('business_license_certificates');
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
    public function reliefs()
    {
        return $this->hasMany(Relief::class);
    }

    // employees
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
    public function leaveTypes()
    {
        return $this->hasMany(LeaveType::class);
    }
    public function leavePeriods()
    {
        return $this->hasMany(LeavePeriod::class);
    }
    public function leaveEntitlements()
    {
        return $this->hasMany(LeaveEntitlement::class);
    }

    //managed businesses
    public function clients()
    {
        return $this->hasMany(Client::class, 'business_id');
    }
    public function locations()
    {
        return $this->hasMany(Location::class);
    }
    public function jobPosts()
    {
        return $this->hasMany(JobPost::class);
    }
    public function applications()
    {
        return $this->hasMany(Application::class);
    }
    public function managedBusinesses()
    {
        return $this->belongsToMany(
            Business::class,
            'clients',
            'business_id',
            'client_business'
        );
    }
    public function managingBusinesses()
    {
        return $this->belongsToMany(
            Business::class,
            'clients',
            'client_business',
            'business_id'
        );
    }
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

}
