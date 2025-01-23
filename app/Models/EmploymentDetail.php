<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmploymentDetail extends Model
{
    protected $fillable = [
        'employee_id',
        'department_id',
        'job_category_id',
        'shift_id',
        'employment_date',
        'probation_end_date',
        'contract_end_date',
        'retirement_date',
        'employment_status',
        'job_description'
    ];

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

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
}
