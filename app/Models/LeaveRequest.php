<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\ModelStatus\HasStatuses;

class LeaveRequest extends Model
{
    use HasFactory, HasStatuses;

    protected $fillable = [
        'reference_number',
        'employee_id',
        'business_id',
        'leave_type_id',
        'start_date',
        'end_date',
        'total_days',
        'half_day',
        'half_day_type',
        'reason',
        'approved_by',
        'approved_at',
        'rejection_reason',
    ];

    protected $casts = [
        'half_day' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
        'total_days' => 'integer',
        'approved_by' => 'integer',
        'approved_at' => 'datetime',
    ];


    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public static function generateUniqueReferenceNumber($businessId)
    {
        do {
            $referenceNumber = 'LR' . strtoupper(substr(uniqid(), -6));
        } while (self::where('business_id', $businessId)->where('reference_number', $referenceNumber)->exists());

        return $referenceNumber;
    }
}
