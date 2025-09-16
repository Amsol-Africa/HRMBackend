<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\LeaveType;

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
        'requires_documentation',
        'is_tentative',
        'current_approval_level',
        'approval_history', // json
        'half_day',
        'half_day_type',
        'reason',
        'approved_by',
        'approved_at',
        'rejection_reason',
    ];

    protected $casts = [
        'half_day'               => 'boolean',
        'start_date'             => 'date',
        'end_date'               => 'date',
        'total_days'             => 'float',
        'approved_by'            => 'integer',
        'approved_at'            => 'datetime',
        'requires_documentation' => 'boolean',
        'is_tentative'           => 'boolean',
        'current_approval_level' => 'integer',
        'approval_history'       => 'array',
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

    /**
     * Existing overlap check — considers pending + approved; excludes rejected.
     */
    public static function hasOverlap($employeeId, $startDate, $endDate, $excludeId = null)
    {
        $start = $startDate instanceof Carbon ? $startDate->toDateString() : Carbon::parse($startDate)->toDateString();
        $end   = $endDate instanceof Carbon ? $endDate->toDateString() : Carbon::parse($endDate)->toDateString();

        $query = self::where('employee_id', $employeeId)
            ->whereNull('rejection_reason')
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('start_date', [$start, $end])
                  ->orWhereBetween('end_date', [$start, $end])
                  ->orWhere(function ($q2) use ($start, $end) {
                      $q2->where('start_date', '<=', $start)
                         ->where('end_date', '>=', $end);
                  });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Calculate inclusive total days taking into account excluded_days from leaveType (weekdays).
     */
    public static function calculateTotalDays($startDate, $endDate, $halfDay = false, $leaveType = null)
    {
        $start = Carbon::parse($startDate)->startOfDay();
        $end   = Carbon::parse($endDate)->startOfDay();

        // Get excluded weekdays from leaveType
        $excluded = [];
        if ($leaveType instanceof LeaveType) {
            $excluded = $leaveType->excluded_days ?? [];
            $excluded = array_map('strtolower', (array)$excluded);
        }

        $period = CarbonPeriod::create($start->toDateString(), $end->toDateString());

        $days = 0;
        foreach ($period as $date) {
            $weekday = strtolower($date->format('l')); // monday, tuesday, ...
            if (!in_array($weekday, $excluded)) {
                $days++;
            }
        }

        if ($halfDay) {
            $days -= 0.5;
        }

        return max(0, (float)$days);
    }

    /**
     * Auto-calc total_days before saving; respect leave type excluded days.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($leaveRequest) {
            // Load leaveType if possible (use relation or query)
            $leaveType = null;
            if ($leaveRequest->leaveType) {
                $leaveType = $leaveRequest->leaveType;
            } elseif ($leaveRequest->leave_type_id) {
                $leaveType = LeaveType::find($leaveRequest->leave_type_id);
            }

            $leaveRequest->total_days = self::calculateTotalDays(
                $leaveRequest->start_date,
                $leaveRequest->end_date,
                $leaveRequest->half_day ?? false,
                $leaveType
            );

            if ($leaveRequest->total_days < 0) {
                $leaveRequest->total_days = 0;
            }
        });
    }
}
