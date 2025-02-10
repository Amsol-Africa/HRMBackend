<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeePayroll extends Model
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
        'housing_levy',
        'taxable_income',
        'paye',
        'personal_relief',
        'pay_after_tax',
        'loan_repayment',
        'advance_recovery',
        'deductions_after_tax',
        'net_pay'
    ];

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public static function calculatePayslip($employee)
    {
        $basic_salary = $employee->salary;
        $housing_allowance = $employee->housing_allowance ?? 0;
        $gross_pay = $basic_salary + $housing_allowance;

        // Compute statutory deductions dynamically
        $nhif = PayrollFormula::calculate('nhif', $gross_pay);
        $nssf = PayrollFormula::calculate('nssf', $gross_pay);
        $housing_levy = PayrollFormula::calculate('housing_levy', $gross_pay);

        // Compute taxable income
        $taxable_income = $gross_pay - ($nssf + $housing_levy);

        // Compute PAYE dynamically
        $paye = PayrollFormula::calculate('paye', $taxable_income);

        // Apply personal relief from DB
        $personal_relief = PayrollFormula::getFixedAmount('personal-relief');
        $pay_after_tax = max(0, $paye - $personal_relief);

        // Additional deductions
        $deductions_after_tax = $employee->advance + $employee->welfare;

        // Compute Net Pay
        $net_pay = $gross_pay - ($pay_after_tax + $deductions_after_tax);

        return self::create(compact(
            'employee_id',
            'basic_salary',
            'housing_allowance',
            'gross_pay',
            'nhif',
            'nssf',
            'housing_levy',
            'taxable_income',
            'paye',
            'personal_relief',
            'pay_after_tax',
            'deductions_after_tax',
            'net_pay'
        ));
    }
}
