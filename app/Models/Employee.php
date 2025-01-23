<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'employee_code',
        'department_id',
        'business_id',

        'gender',
        'date_of_birth',
        'marital_status',
        'national_id',

        'tax_no',
        'nhif_no',
        'nssf_no',
        'blood_group',
        'passport_no',

        'passport_issue_date',
        'passport_expiry_date',
    ];

    // Define relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function job_category()
    {
        return $this->belongsTo(Business::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    public function employmentDetails()
    {
        return $this->hasOne(EmploymentDetail::class);
    }

    public function nextOfKin()
    {
        return $this->hasMany(EmployeeNextOfKin::class);
    }

    public function paymentDetails()
    {
        return $this->hasOne(EmployeePaymentDetail::class);
    }

    public function contactDetails()
    {
        return $this->hasOne(EmployeeContactDetail::class);
    }

    public function documents()
    {
        return $this->hasMany(EmployeeDocument::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cv_attachments');
        $this->addMediaCollection('academic_files');
    }
    public function getImageUrl()
    {
        $media = $this->getFirstMedia('avatars');
        if ($media && File::exists($media->getPath())) {
            return $media->getUrl();
        }
        return asset('media/avatar.png');
    }
}
