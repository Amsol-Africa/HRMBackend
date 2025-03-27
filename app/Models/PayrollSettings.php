<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollSettings extends Model
{
    protected $fillable = [
        'employee_id',
        'year',
        'month',
        'allowances',
        'deductions',
        'reliefs',
        'overtime',
        'loans',
        'advances',
        'absenteeism_charge'
    ];

    protected $casts = [
        'allowances' => 'array',
        'deductions' => 'array',
        'reliefs' => 'array',
        'overtime' => 'array',
        'loans' => 'array',
        'advances' => 'array',
        'absenteeism_charge' => 'float',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}