<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RosterAssignment extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'roster_id',
        'employee_id',
        'department_id',
        'job_category_id',
        'location_id',
        'date',
        'shift_id',
        'leave_id',
        'status',
        'overtime_hours',
        'notes',
        'notification_status',
        'notification_type',
    ];

    protected $casts = [
        'date' => 'date',
        'overtime_hours' => 'decimal:2',
    ];

    public function roster()
    {
        return $this->belongsTo(Roster::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function jobCategory()
    {
        return $this->belongsTo(JobCategory::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function leave()
    {
        return $this->belongsTo(LeaveType::class);
    }
}