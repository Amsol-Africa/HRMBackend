<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\LoanRepayment;
use App\Models\PayrollFormula;
use App\Traits\HandleTransactions;
use Carbon\Carbon;

class PayrollService
{
    use HandleTransactions;

    public function calculatePayroll(Employee $employee, $startDate, $endDate, $payroll_id)
    {
        return $this->handleTransaction(function () use ($employee, $startDate, $endDate, $payroll_id) {

            $grossPay = $employee->paymentDetails->basic_salary;

            // Include allowances in gross pay
            foreach ($employee->allowances as $allowance) {
                $grossPay += $allowance->amount ?? 0;
            }

            // Get all reliefs from the pivot table
            $employeeReliefs = $employee->reliefs()->get();

            // Calculate Taxable Income by deducting before-tax reliefs
            $taxableIncome = $grossPay;
            foreach ($employeeReliefs as $relief) {
                if ($relief->tax_application === 'before_tax') {
                    $taxableIncome -= $relief->pivot->amount ?? 0;
                }
            }

            // Compute statutory deductions
            $paye = PayrollFormula::calculate('paye', $taxableIncome);
            $nssf = PayrollFormula::calculate('nssf', $grossPay);
            $nhif = PayrollFormula::calculate('nhif', $grossPay);
            $housingLevy = PayrollFormula::calculate('housing-levy', $grossPay);

            // Calculate Pay After Tax
            $payAfterTax = $grossPay - $paye;

            // Calculate Other Deductions (Non-statutory deductions)
            $otherDeductions = 0;
            foreach ($employee->deductions as $deduction) {
                $otherDeductions += $deduction->amount ?? 0;
            }

            // Process Loans: Deduct active loan repayments and record in loan_repayments table
            $loanDeductions = 0;
            $activeLoans = $employee->loans()->whereHas('statuses', function ($query) {
                $query->where('name', 'active');
            })->get();

            foreach ($activeLoans as $loan) {
                $monthlyInstallment = $loan->amount / $loan->term_months;
                $repaidAmount = $loan->repayments()->sum('amount_paid');
                $remainingBalance = $loan->amount - $repaidAmount;
                $deductionAmount = min($monthlyInstallment, $remainingBalance);
                $loanDeductions += $deductionAmount;

                // Record repayment
                if ($deductionAmount > 0) {
                    LoanRepayment::create([
                        'loan_id' => $loan->id,
                        'repayment_date' => Carbon::now(),
                        'amount_paid' => $deductionAmount,
                        'notes' => "Automated payroll deduction",
                    ]);
                }

                // If the loan is fully repaid, mark it as "completed"
                if ($repaidAmount + $deductionAmount >= $loan->amount) {
                    $loan->setStatus('completed');
                }
            }
            $otherDeductions += $loanDeductions;

            // Process Advances: Deduct any unpaid advances and mark as "paid"
            $advanceDeductions = $employee->advances()->whereHas('statuses', function ($query) {
                $query->where('name', 'unpaid');
            })->sum('amount');

            if ($advanceDeductions > 0) {
                // Mark all unpaid advances as "paid"
                $employee->advances()->whereHas('statuses', function ($query) {
                    $query->where('name', 'unpaid');
                })->each(function ($advance) {
                    $advance->setStatus('paid');
                });
            }

            $otherDeductions += $advanceDeductions;

            // Apply after-tax reliefs
            foreach ($employeeReliefs as $relief) {
                if ($relief->tax_application === 'after_tax') {
                    $payAfterTax += $relief->pivot->amount ?? 0;
                }
            }

            // Final Net Pay Calculation
            $netPay = $payAfterTax - $otherDeductions;

            // Store Payroll Data
            $employee->payrolls()->create([
                'payroll_id' => $payroll_id,
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
