<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;
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
    public static function getEmployeePayrollByMonthYear($employeeId, $year = null, $month = null)
    {
        Log::debug($year);
        Log::debug($month);
        if ($year && $month) {
            return self::whereHas('payroll', function ($query) use ($year, $month) {
                $query->where('payrun_year', $year)->where('payrun_month', $month);
            })->where('employee_id', $employeeId)->first();
        }

        return self::where('employee_id', $employeeId)
            ->latest('created_at')
            ->first();
    }



}

