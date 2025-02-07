<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\PayrollFormula;
use App\Traits\HandleTransactions;

class PayrollService
{
    use HandleTransactions;

    public function calculatePayroll(Employee $employee, $startDate, $endDate, $payroll_id)
    {
        return $this->handleTransaction(function () use ($employee, $startDate, $endDate, $payroll_id) {
            // Access basic salary from the related paymentDetails model
            $grossPay = $employee->paymentDetails->basic_salary;

            // Calculate Allowances
            foreach ($employee->allowances as $allowance) {
                $grossPay += $allowance->amount ?? 0;
            }

            // Calculate Taxable Income
            $taxableIncome = $grossPay;
            foreach ($employee->reliefs as $relief) {
                if ($relief->tax_application === 'before_tax') {
                    $taxableIncome -= $relief->fixed_amount ?? 0;
                }
            }

            // Compute Statutory Deductions using PayrollFormula
            $paye = PayrollFormula::calculate('paye', $taxableIncome);
            $nssf = PayrollFormula::calculate('nssf', $grossPay);
            $nhif = PayrollFormula::calculate('nhif', $grossPay);
            $housingLevy = PayrollFormula::calculate('housing-levy', $grossPay);

            // Calculate Pay After Tax
            $payAfterTax = $grossPay - $paye;

            // Calculate Other Deductions
            $otherDeductions = 0;
            foreach ($employee->deductions as $deduction) {
                $otherDeductions += $deduction->amount ?? 0;
            }

            // Calculate Post Tax Reliefs
            foreach ($employee->reliefs as $relief) {
                if ($relief->tax_application === 'after_tax') {
                    $payAfterTax += $relief->fixed_amount ?? 0;
                }
            }

            // Calculate Net Pay
            $netPay = $payAfterTax - $otherDeductions;

            // Store Payroll Data
            $employee->payrolls()->create([
                'payroll_id' => $payroll_id, // Assign appropriate payroll ID
                'basic_salary' => $employee->paymentDetails->basic_salary,
                'gross_pay' => $grossPay,
                'taxable_income' => $taxableIncome,
                'paye' => $paye,
                'nssf' => $nssf,
                'nhif' => $nhif,
                'housing_levy' => $housingLevy,
                'pay_after_tax' => $payAfterTax,
                'deductions_after_tax' => $otherDeductions,
                'net_pay' => $netPay,
            ]);

            return [
                'gross_pay' => $grossPay,
                'taxable_income' => $taxableIncome,
                'paye' => $paye,
                'nssf' => $nssf,
                'nhif' => $nhif,
                'housing_levy' => $housingLevy,
                'pay_after_tax' => $payAfterTax,
                'other_deductions' => $otherDeductions,
                'net_pay' => $netPay,
            ];
        });
    }
}
