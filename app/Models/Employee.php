<?php

namespace App\Models;

use Carbon\Carbon;
use App\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\ModelStatus\HasStatuses;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, HasStatuses, LogsActivity;

    protected $fillable = [
        'user_id',
        'employee_code',
        'department_id',
        'business_id',
        'location_id',

        'gender',
        'alternate_phone',
        'date_of_birth',
        'place_of_birth',
        'marital_status',
        'national_id',
        'place_of_issue',

        'tax_no',
        'nhif_no',
        'nssf_no',
        'passport_no',
        'passport_issue_date',
        'passport_expiry_date',
        'address',
        'permanent_address',
        'blood_group',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'passport_issue_date' => 'date',
        'passport_expiry_date' => 'date',
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

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function spouse()
    {
        return $this->hasOne(Spouse::class);
    }

    public function academicDetails()
    {
        return $this->hasMany(AcademicQualification::class);
    }

    public function previousEmployment()
    {
        return $this->hasOne(PreviousEmployment::class);
    }

    public function emergencyContacts()
    {
        return $this->hasMany(EmergencyContact::class);
    }

    public function familyMembers()
    {
        return $this->hasMany(EmployeeFamilyMember::class);
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

    public function reliefs()
    {
        return $this->belongsToMany(Relief::class, 'employee_reliefs')->withPivot('amount')->withTimestamps();
    }

    public function deductions()
    {
        return $this->hasMany(EmployeeDeduction::class);
    }

    public function allowances()
    {
        return $this->hasMany(EmployeeAllowance::class);
    }

    public function payrolls()
    {
        return $this->hasMany(EmployeePayroll::class);
    }

    public function advances()
    {
        return $this->hasMany(Advance::class);
    }

    public function overtimes()
    {
        return $this->hasMany(Overtime::class);
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    // Added Task Relationship (Many-to-Many)
    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'employee_task')->withTimestamps();
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }
    public function scopeOnLeave($query)
    {
        \Log::debug('Starting onLeave scope - Model: ' . $query->getModel()->getTable()); // Log the initial model (Employee)

        return $query->whereHas('leaveRequests', function ($query) {
            \Log::debug('Inside whereHas - Model: ' . $query->getModel()->getTable()); // Log the related model (LeaveRequest)

            $query->where('approved_at', '!=', null)
                ->whereDate('start_date', '<=', Carbon::today())
                ->whereDate('end_date', '>=', Carbon::today())
                ->currentStatus('active');
        });
    }
    public function getRemainingLeaveDaysAttribute()
    {
        $totalAllocated = $this->total_leave_days ?? 0; // Assuming a column storing total leave entitlement
        $usedLeave = $this->leaveRequests()->where('status', 'Approved')->sum('total_days');

        return max($totalAllocated - $usedLeave, 0);
    }



}
