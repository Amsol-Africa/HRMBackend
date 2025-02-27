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

            $business_id = $employee->business_id;
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

            // Fetch all payroll formulas for the business
            $payrollDeductions = PayrollFormula::where('business_id', $business_id)
                ->orWhereNull('business_id') // Allow system-wide formulas if business-specific ones don't exist
                ->get();

            // Compute deductions dynamically
            $deductions = [];
            foreach ($payrollDeductions as $formula) {
                $basis = ($formula->calculation_basis === 'taxable_income') ? $taxableIncome : $grossPay;
                $deductions[$formula->slug] = PayrollFormula::calculateForBusiness($formula->name, $basis, $business_id);
            }

            // Dynamically find PAYE formula
            $payeFormula = PayrollFormula::where('business_id', $business_id)
                ->orWhereNull('business_id') // Allow system-wide PAYE formula
                ->where('name', 'LIKE', '%PAYE%') // Find any formula that contains "PAYE"
                ->first();

            $payeSlug = $payeFormula ? $payeFormula->slug : null;

            // Compute pay after tax
            $payAfterTax = $grossPay - ($deductions[$payeSlug] ?? 0);


            // Calculate Other Deductions (Non-statutory deductions)
            $otherDeductions = 0;
            foreach ($employee->deductions as $deduction) {
                $otherDeductions += $deduction->amount ?? 0;
            }

            // Process Loans
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

            // Process Advances
            $advanceDeductions = $employee->advances()->whereHas('statuses', function ($query) {
                $query->where('name', 'unpaid');
            })->sum('amount');

            if ($advanceDeductions > 0) {
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
                'pay_after_tax' => $payAfterTax,
                'deductions_after_tax' => $otherDeductions,
                'net_pay' => $netPay,
                'deductions' => json_encode($deductions), // Store all calculated deductions
            ]);

            return array_merge([
                'gross_pay' => $grossPay,
                'taxable_income' => $taxableIncome,
                'pay_after_tax' => $payAfterTax,
                'other_deductions' => $otherDeductions,
                'net_pay' => $netPay,
            ], $deductions);
        });
    }
}
