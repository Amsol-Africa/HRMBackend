<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class LeaveRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_number',
        'employee_id',
        'business_id',
        'leave_type_id',
        'start_date',
        'end_date',
        'total_days',
        'attachment',
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
        'total_days' => 'float', // allow decimals for half-days
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

    /**
     * Scope for leave `status` based on approved_by / rejection_reason.
     */
    public function scopeStatus($query, $statusName)
    {
        switch (strtolower($statusName)) {
            case 'pending':
                return $query->whereNull('approved_by')->whereNull('rejection_reason');
            case 'approved':
                return $query->whereNotNull('approved_by')->whereNull('rejection_reason');
            case 'rejected':
            case 'declined':
                return $query->whereNotNull('rejection_reason');
            default:
                return $query;
        }
    }

    public function scopeCurrentStatus($query, $statusName)
    {
        return $this->scopeStatus($query, $statusName);
    }

    public static function generateUniqueReferenceNumber($businessId)
    {
        do {
            $referenceNumber = 'LR' . strtoupper(substr(uniqid(), -6));
        } while (self::where('business_id', $businessId)->where('reference_number', $referenceNumber)->exists());

        return $referenceNumber;
    }

    public static function hasOverlap($employeeId, $startDate, $endDate)
    {
        return self::where('employee_id', $employeeId)
            // Only consider approved or pending (exclude rejected)
            ->where(function ($q) {
                $q->where(function ($q1) {
                    // Approved
                    $q1->whereNotNull('approved_by')
                        ->whereNull('rejection_reason');
                })
                ->orWhere(function ($q2) {
                    // Pending
                    $q2->whereNull('approved_by')
                        ->whereNull('rejection_reason');
                });
            })
            // Overlap condition
            ->where('start_date', '<=', $endDate)
            ->where('end_date', '>=', $startDate)
            ->exists();
    }

    /**
     * Calculate leave days, inclusive of start and end date.
     * Supports half-day adjustment.
     */
    public static function calculateTotalDays($startDate, $endDate, $halfDay = false)
    {
        $start = Carbon::parse($startDate)->startOfDay();
        $end   = Carbon::parse($endDate)->endOfDay();

        // Inclusive difference
        $days = $start->diffInDays($end);

        if ($halfDay) {
            $days -= 0.5;
        }

        return $days;
    }

    /**
     * Boot method to always calculate days before saving.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($leaveRequest) {
            $leaveRequest->total_days = self::calculateTotalDays(
                $leaveRequest->start_date,
                $leaveRequest->end_date,
                $leaveRequest->half_day
            );
        });
    }
}
            