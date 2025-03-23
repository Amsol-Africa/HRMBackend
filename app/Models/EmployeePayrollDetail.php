<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeePayrollDetail extends Model
{
    protected $fillable = [
        'employee_id',
        'business_id',
        'has_insurance',
        'insurance_premium',
        'has_mortgage',
        'mortgage_interest',
        'has_hosp',
        'hosp_deposit',
        'has_helb',
        'has_disability_exemption'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
