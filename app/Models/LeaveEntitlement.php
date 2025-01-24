<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\ModelStatus\HasStatuses;

class LeaveEntitlement extends Model
{
    use HasFactory, HasStatuses;

    protected $fillable = [
        'business_id',
        'employee_id',
        'leave_type_id',
        'leave_period_id',
        'carry_forward',
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
}
