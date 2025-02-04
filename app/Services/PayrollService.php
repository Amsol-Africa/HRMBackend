<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\PayrollFormula;
use App\Models\PayrollFormulaBracket;
use App\Models\Relief;
use Illuminate\Support\Facades\DB; // For database transactions
use Exception; // For custom exceptions

class PayrollService
{
    public function calculatePayroll(Employee $employee, $startDate, $endDate)
    {
        try {
            DB::beginTransaction(); // Start a database transaction

            $grossPay = $employee->basic_salary;

            // Calculate Allowances
            foreach ($employee->allowances as $allowance) {
                $expression = $allowance->formula_expression?? ($allowance->amount?? 0);
                $resolvedExpression = $this->resolvePlaceholders($expression, ['basic_pay' => $employee->basic_salary, 'gross_pay' => $grossPay, 'amount' => $allowance->amount]);
                $allowanceAmount = $this->evaluateExpression($resolvedExpression);
                $grossPay += $allowanceAmount;
            }

            // Calculate Taxable Income
            $taxableIncome = $grossPay;
            foreach ($employee->reliefs as $relief) {
                if ($relief->tax_application === 'before_tax') {
                    $expression = $relief->formula_expression?? ($relief->fixed_amount?? 0);
                    $resolvedExpression = $this->resolvePlaceholders($expression, ['taxable_income' => $taxableIncome, 'gross_pay' => $grossPay, 'fixed_amount' => $relief->fixed_amount]);
                    $reliefAmount = $this->evaluateExpression($resolvedExpression);
                    $taxableIncome -= $reliefAmount;
                }
            }

            // Calculate Statutory Deductions
            $paye = $this->calculateDeduction($employee, 'paye', $taxableIncome);
            $nssf = $this->calculateDeduction($employee, 'nssf', $grossPay);
            $nhif = $this->calculateDeduction($employee, 'nhif', $grossPay);
            $housingLevy = $this->calculateDeduction($employee, 'housing_levy', $employee->basic_salary);


            $payAfterTax = $grossPay - $paye;

            // Calculate Other Deductions
            $otherDeductions = 0;
            foreach ($employee->deductions as $deduction) {
                $expression = $deduction->formula_expression?? ($deduction->amount?? 0);
                $resolvedExpression = $this->resolvePlaceholders($expression, ['pay_after_tax' => $payAfterTax, 'gross_pay' => $grossPay, 'amount' => $deduction->amount]);
                $deductionAmount = $this->evaluateExpression($resolvedExpression);
                $otherDeductions += $deductionAmount;
            }

            // Calculate Post Tax Reliefs
            foreach ($employee->reliefs as $relief) {
                if ($relief->tax_application === 'after_tax') {
                    $expression = $relief->formula_expression?? ($relief->fixed_amount?? 0);
                    $resolvedExpression = $this->resolvePlaceholders($expression, ['pay_after_tax' => $payAfterTax, 'gross_pay' => $grossPay, 'fixed_amount' => $relief->fixed_amount]);
                    $reliefAmount = $this->evaluateExpression($resolvedExpression);
                    $payAfterTax += $reliefAmount;
                }
            }

            $netPay = $payAfterTax - $otherDeductions;

            // Store Results in employee_payrolls table
            $employee->payrolls()->create([
                'payroll_id' => ""/* Your payroll ID */,
                'basic_salary' => $employee->basic_salary,
                'gross_pay' => $grossPay,
                'taxable_income' => $taxableIncome,
                'paye' => $paye,
                'nssf' => $nssf,
                'nhif' => $nhif,
                'housing_levy' => $housingLevy,
                'pay_after_tax' => $payAfterTax,
                'deductions_after_tax' => $otherDeductions,
                'net_pay' => $netPay,
                //... other payroll data
            ]);

            DB::commit(); // Commit the transaction

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
                //... other payroll data
            ];
        } catch (Exception $e) {
            DB::rollBack(); // Rollback the transaction in case of error
            // Log the error or handle it appropriately
            throw new Exception("Payroll calculation failed: ". $e->getMessage()); // Re-throw the exception
            //Or return an error response
            //return response()->json(['error' => $e->getMessage()], 500);

        }
    }

    private function calculateDeduction(Employee $employee, $deductionSlug, $calculationBasisValue)
    {
        $formula = PayrollFormula::where('slug', $deductionSlug)->first();
        if ($formula) {
            if ($formula->formula_type === 'bracket') {
                $bracket = PayrollFormulaBracket::where('payroll_formula_id', $formula->id)
                    ->where('min', '<=', $calculationBasisValue)
                    ->where(function ($query) use ($calculationBasisValue) {
                        $query->whereNull('max')->orWhere('max', '>=', $calculationBasisValue);
                    })
                    ->orderBy('min', 'desc') // Important for correct bracket selection
                    ->first();

                if ($bracket) {
                    return $bracket->rate? $calculationBasisValue * $bracket->rate: $bracket->amount;
                }
            } else if ($formula->formula_type === 'expression') {
                $resolvedExpression = $this->resolvePlaceholders($formula->formula_expression, [$formula->calculation_basis => $calculationBasisValue]);
                return $this->evaluateExpression($resolvedExpression);
            }
        }
        return 0; // Or handle the case where the formula is not found
    }

    private function resolvePlaceholders($expression, $variables)
    {
        foreach ($variables as $key => $value) {
            $expression = str_replace('${'. $key. '}', $value, $expression);
        }
        return $expression;
    }


    private function evaluateExpression($expression)
    {
        // Option 1: Using eval() (with EXTREME caution - only for basic, trusted expressions)
        // return eval('return '. $expression. ';');  // SECURITY RISK - DO NOT USE IN PRODUCTION UNLESS SANITIZED

        // Option 2: Using a third-party library (RECOMMENDED)
        // Example using "scottehansen/expression-parser" (install via Composer)
        // composer require scottehansen/expression-parser
        $parser = new \Scotthansen\ExpressionParser\Parser();
        try {
            return $parser->evaluate($expression);
        } catch (\Exception $e) {
            // Handle parsing errors (e.g., log, throw exception)
            throw new Exception("Expression parsing error: ". $e->getMessage(). " in expression: ". $expression);
            // Or return a default value (e.g., 0)
            //return 0;
        }

        // Option 3: Simple Custom Evaluation (for basic arithmetic - not recommended for complex logic)
        //  (Implementation would go here - very limited and error-prone for anything beyond +, -, *, /, %)
    }
}
