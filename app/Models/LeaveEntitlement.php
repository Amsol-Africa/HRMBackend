<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Spatie\ModelStatus\HasStatuses;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeaveEntitlement extends Model
{
    use HasFactory, HasStatuses, LogsActivity;

    protected $fillable = [
        'business_id',
        'employee_id',
        'leave_type_id',
        'leave_period_id',
        'entitled_days',
        'accrued_days',
        'total_days',
        'days_taken',
        'days_remaining',
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
    public function leavePeriod()
    {
        return $this->belongsTo(LeavePeriod::class);
    }
    public function calculateRemainingDays()
    {
        $this->days_remaining = $this->entitled_days - $this->days_taken;
        $this->save();
    }

        // app/Models/LeaveEntitlement.php
    public function getRemainingDays()
    {
        $usedDays = LeaveRequest::where('employee_id', $this->employee_id)
            ->where('leave_type_id', $this->leave_type_id)
            ->whereNotNull('approved_by') // only count approved
            ->whereNull('rejection_reason')
            ->sum('total_days');

        return max(0, $this->days - $usedDays);
    }

}
