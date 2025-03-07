<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeePayroll extends Model //problem with this model methods
{
    protected $fillable = [
        'payroll_id',
        'employee_id',
        'basic_salary',
        'housing_allowance',
        'gross_pay',
        'overtime',
        'nhif',
        'nssf',
        'paye',
        'housing_levy',
        'taxable_income',
        'personal_relief',
        'pay_after_tax',
        'loan_repayment',
        'advance_recovery',
        'deductions_after_tax',
        'net_pay',
        'deductions'
    ];

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

}

