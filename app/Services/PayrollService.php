<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Employee;
use App\Models\LoanRepayment;
use App\Models\PayrollFormula;
use App\Traits\HandleTransactions;
use Illuminate\Support\Facades\Log;

class PayrollService
{
    use HandleTransactions;

    public function calculatePayroll(Employee $employee, $startDate, $endDate, $payroll_id)
    {
        return $this->handleTransaction(function () use ($employee, $startDate, $endDate, $payroll_id) {
            $business_id = $employee->business_id;
            $overtimePay = $employee->overtimes()->sum('total_pay');
            $grossPay = $this->calculateGrossPay($employee, $overtimePay);
            $taxableIncome = $this->calculateTaxableIncome($employee, $grossPay);
            $deductions = $this->calculateDeductions($employee, $business_id, $taxableIncome, $grossPay);
            $payData = $this->calculatePayData($employee, $grossPay, $deductions);
            $payAfterTax = $payData['payAfterTax'];
            $personalRelief = $payData['personalRelief'];
            $totalDeductions = $payData['totalDeductions'];
            $otherDeductions = $this->calculateOtherDeductions($employee);
            $totalOtherDeductions = array_sum(array_column($otherDeductions, 'amount'));
            Log::debug($payAfterTax);
            Log::debug($totalDeductions);
            Log::debug($totalOtherDeductions);
            $netPay = $this->calculateNetPay($employee, $payAfterTax, $totalOtherDeductions);

            $this->storePayrollData(
                $employee,
                $payroll_id,
                $grossPay,
                $taxableIncome,
                $payAfterTax,
                $totalOtherDeductions,
                $netPay,
                $otherDeductions,
                $overtimePay,
                $deductions['nhif'],
                $deductions['nssf'],
                $deductions['housing-levy'],
                $deductions['paye'],
                $personalRelief
            );

            return array_merge([
                'gross_pay' => $grossPay,
                'taxable_income' => $taxableIncome,
                'pay_after_tax' => $payAfterTax,
                'other_deductions' => $otherDeductions,
                'net_pay' => $netPay,
            ], $deductions);
        });
    }

    private function calculateGrossPay(Employee $employee, float $overtimePay): float
    {
        $grossPay = $employee->paymentDetails->basic_salary;
        foreach ($employee->allowances as $allowance) {
            $grossPay += $allowance->amount ?? 0;
        }
        $grossPay += $overtimePay;
        return $grossPay;
    }

    private function calculateTaxableIncome(Employee $employee, float $grossPay): float
    {
        $taxableIncome = $grossPay;
        foreach ($employee->reliefs as $relief) {
            if ($relief->tax_application === 'before_tax') {
                $taxableIncome -= $relief->pivot->amount ?? 0;
            }
        }
        return $taxableIncome;
    }

    private function calculateDeductions(Employee $employee, int $business_id, float $taxableIncome, float $grossPay): array
    {
        $payrollDeductions = PayrollFormula::where('business_id', $business_id)
            ->orWhereNull('business_id')
            ->get();

        $deductions = [];
        foreach ($payrollDeductions as $formula) {
            $basisAmount = ($formula->calculation_basis === 'taxable_income') ? $taxableIncome : $grossPay;
            $deductions[$formula->slug] = PayrollFormula::calculateForBusiness($formula->name, $basisAmount, $business_id);
        }

        return $deductions;
    }

    private function calculatePayData(Employee $employee, float $grossPay, array $deductions): array
    {
        // Sum all statutory deductions
        $totalDeductions = array_sum($deductions);
        $payAfterTax = $grossPay - $totalDeductions;

        $payAfterTax = max(0, $payAfterTax);

        $personalRelief = PayrollFormula::getFixedAmount('personal-relief');

        foreach ($employee->reliefs as $relief) {
            if ($relief->tax_application === 'after_tax') {
                $payAfterTax += $relief->pivot->amount ?? 0;
            }
        }

        return [
            'payAfterTax' => $payAfterTax,
            'personalRelief' => $personalRelief,
            'totalDeductions' => $totalDeductions,
        ];
    }

    private function calculateOtherDeductions(Employee $employee): array
    {
        $otherDeductions = [];

        // Employee Deductions
        foreach ($employee->deductions as $deduction) {
            $otherDeductions[] = [
                'type' => 'Employee Deduction',
                'name' => $deduction->deduction->name,
                'amount' => $deduction->amount,
                'notes' => $deduction->notes,
            ];
        }

        // Loans
        $loanDetails = $this->processLoans($employee);
        $otherDeductions = array_merge($otherDeductions, $loanDetails);

        // Advances
        $advanceDetails = $this->processAdvances($employee);
        $otherDeductions = array_merge($otherDeductions, $advanceDetails);

        return $otherDeductions;
    }

    private function processLoans(Employee $employee): array
    {
        $loanDeductions = [];
        $activeLoans = $employee->loans()->whereHas('statuses', function ($query) {
            $query->where('name', 'active');
        })->get();

        foreach ($activeLoans as $loan) {
            $monthlyInstallment = $loan->amount / $loan->term_months;
            $repaidAmount = $loan->repayments()->sum('amount_paid');
            $remainingBalance = $loan->amount - $repaidAmount;
            $deductionAmount = min($monthlyInstallment, $remainingBalance);

            if ($deductionAmount > 0) {
                LoanRepayment::create([
                    'loan_id' => $loan->id,
                    'repayment_date' => Carbon::now(),
                    'amount_paid' => $deductionAmount,
                    'notes' => "Automated payroll deduction",
                ]);

                $loanDeductions[] = [
                    'type' => 'Loan Repayment',
                    'name' => $loan->name,
                    'amount' => $deductionAmount,
                    'loan_id' => $loan->id,
                ];
            }

            if ($repaidAmount + $deductionAmount >= $loan->amount) {
                $loan->setStatus('completed');
            }
        }
        return $loanDeductions;
    }

    private function processAdvances(Employee $employee): array
    {
        $advanceDeductions = [];
        $advances = $employee->advances()->whereHas('statuses', function ($query) {
            $query->where('name', 'unpaid');
        })->get();

        foreach ($advances as $advance) {
            $advanceDeductions[] = [
                'type' => 'Advance Payment',
                'name' => 'Advance',
                'amount' => $advance->amount,
                'advance_id' => $advance->id,
            ];
            $advance->setStatus('paid');
        }

        return $advanceDeductions;
    }

    private function calculateNetPay(Employee $employee, float $payAfterTax, float $otherDeductions): float
    {
        return $payAfterTax - $otherDeductions;
    }

    private function storePayrollData(Employee $employee, $payroll_id, $grossPay, $taxableIncome, $payAfterTax, $otherDeductions, $netPay, $deductions, $overtimePay, $nhif, $nssf, $housingLevy, $paye, $personalRelief): void
    {
        $employee->payrolls()->create([
            'payroll_id' => $payroll_id,
            'employee_id' => $employee->id,
            'basic_salary' => $employee->paymentDetails->basic_salary,
            'gross_pay' => $grossPay,
            'taxable_income' => $taxableIncome,
            'pay_after_tax' => $payAfterTax,
            'deductions_after_tax' => $otherDeductions,
            'net_pay' => $netPay,
            'deductions' => json_encode($deductions),
            'overtime' => $overtimePay,
            'nhif' => $nhif,
            'nssf' => $nssf,
            'housing_levy' => $housingLevy,
            'paye' => $paye,
            'personal_relief' => $personalRelief,
        ]);
    }
}
