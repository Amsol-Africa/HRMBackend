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
            $payeData = $this->calculatePayeData($employee, $grossPay, $deductions);
            $payAfterTax = $payeData['payAfterTax'];
            $personalRelief = $payeData['personalRelief'];
            $otherDeductions = $this->calculateOtherDeductions($employee);
            $netPay = $this->calculateNetPay($employee, $payAfterTax, $otherDeductions);

            $this->storePayrollData($employee, $payroll_id, $grossPay, $taxableIncome, $payAfterTax, $otherDeductions, $netPay, $deductions, $overtimePay, $deductions['nhif'], $deductions['nssf'], $deductions['housing-levy'], $deductions['paye'], $personalRelief);

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

    private function calculatePayeData(Employee $employee, float $grossPay, array $deductions): array
    {
        $payeFormula = PayrollFormula::where('business_id', $employee->business_id)
            ->orWhereNull('business_id')
            ->where('name', 'LIKE', '%PAYE%')
            ->first();

        $payeSlug = $payeFormula ? $payeFormula->slug : null;
        $payeAmount = $deductions[$payeSlug] ?? 0;
        $payAfterTax = $grossPay - $payeAmount;

        $personalRelief = PayrollFormula::getFixedAmount('personal-relief');
        $payAfterTax = max(0, $payAfterTax);

        foreach ($employee->reliefs as $relief) {
            if ($relief->tax_application === 'after_tax') {
                $payAfterTax += $relief->pivot->amount ?? 0;
            }
        }
        return ['payAfterTax' => $payAfterTax, 'personalRelief' => $personalRelief];
    }

    private function calculateOtherDeductions(Employee $employee): float
    {
        $otherDeductions = 0;
        foreach ($employee->deductions as $deduction) {
            $otherDeductions += $deduction->amount ?? 0;
        }
        $otherDeductions += $this->processLoans($employee);
        $otherDeductions += $this->processAdvances($employee);
        return $otherDeductions;
    }

    private function processLoans(Employee $employee): float
    {
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

            if ($deductionAmount > 0) {
                LoanRepayment::create([
                    'loan_id' => $loan->id,
                    'repayment_date' => Carbon::now(),
                    'amount_paid' => $deductionAmount,
                    'notes' => "Automated payroll deduction",
                ]);
            }

            if ($repaidAmount + $deductionAmount >= $loan->amount) {
                $loan->setStatus('completed');
            }
        }
        return $loanDeductions;
    }

    private function processAdvances(Employee $employee): float
    {
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
