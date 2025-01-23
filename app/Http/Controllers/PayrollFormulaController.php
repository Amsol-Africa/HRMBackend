<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;
use App\Http\RequestResponse;
use App\Models\PayrollFormula;
use App\Traits\HandleTransactions;
use Illuminate\Support\Facades\Log;
use App\Models\PayrollFormulaBracket;

class PayrollFormulaController extends Controller
{
    use HandleTransactions;

    public function fetch(Request $request)
    {
        $business = Business::findBySlug(session('active_business_slug'));
        $payroll_formulas = $business->payrollFormulas;

        $payroll_formulas_table = view('payroll._table', compact('payroll_formulas'))->render();
        return RequestResponse::ok('Ok', $payroll_formulas_table);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'formula_name' => 'required|string|max:255',
            'calculation_basis' => 'required|in:basic pay,gross pay,cash pay,taxable pay',
            'is_progressive' => 'required|string|in:yes,no',
            'formula_type' => 'required|in:rate,amount',
            'minimum_amount' => 'nullable|numeric|min:0',
            'brackets' => 'nullable|array',
            'brackets.*.min' => 'required_with:brackets|numeric|min:0',
            'brackets.*.max' => 'nullable|numeric|gt:brackets.*.min',
            'brackets.*.rate' => 'required_if:formula_type,rate|numeric|min:0|max:100',
            // 'brackets.*.amount' => 'required_if:formula_type,amount|numeric|min:0',
        ]);

        // Log::debug($request->all());


        return $this->handleTransaction(function () use ($request, $validatedData) {
            $business = Business::findBySlug(session('active_business_slug'));

            $payroll_formula = $business->payrollFormulas()->create([
                'name' => $validatedData['formula_name'],
                'calculation_basis' => $validatedData['calculation_basis'],
                'is_progressive' => $validatedData['is_progressive'] === 'yes',
                'minimum_amount' => $validatedData['minimum_amount'],
                'formula_type' => $validatedData['formula_type'],
            ]);

            if ($validatedData['is_progressive'] && isset($validatedData['brackets'])) {
                foreach ($validatedData['brackets'] as $bracket) {
                    $payroll_formula->brackets()->create($bracket);
                }
            }

            return RequestResponse::created('Payroll formula added successfully.');
        });
    }

    public function edit(Request $request)
    {
        $validatedData = $request->validate([
            'payroll_formula' => 'required|exists:payroll_formulas,id',
        ]);

        $payroll_formula = PayrollFormula::with('brackets')->find($validatedData['payroll_formula']);
        $payroll_formula_form = view('payroll-formulas._form', compact('payroll_formula'))->render();

        return RequestResponse::ok('Ok', $payroll_formula_form);
    }

    public function show(Request $request)
    {
        $validatedData = $request->validate([
            'payroll_formula' => 'required|exists:payroll_formulas,slug',
        ]);

        $payroll_formula = PayrollFormula::where('slug', $validatedData['payroll_formula'])->firstOrFail();
        $brackets = PayrollFormulaBracket::where('payroll_formula_id', $payroll_formula->id)->get();
        Log::debug($brackets);
        $payroll_formulas_details = view('components.payroll-formula-details', compact('payroll_formula', 'brackets'))->render();

        return RequestResponse::ok('Ok', $payroll_formulas_details);
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'payroll_formula_id' => 'required|exists:payroll_formulas,id',
            'formula_name' => 'required|string|max:255',
            'calculation_basis' => 'required|in:basic pay,gross pay,cash pay,taxable pay',
            'is_progressive' => 'required|boolean',
            'minimum_amount' => 'nullable|numeric|min:0',
            'brackets' => 'nullable|array',
            'brackets.*.min' => 'required_with:brackets|numeric|min:0',
            'brackets.*.max' => 'nullable|numeric|gt:brackets.*.min',
            'brackets.*.rate' => 'required_with:brackets|numeric|min:0|max:100',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $payroll_formula = PayrollFormula::find($validatedData['payroll_formula_id']);

            $payroll_formula->update([
                'formula_name' => $validatedData['formula_name'],
                'calculation_basis' => $validatedData['calculation_basis'],
                'is_progressive' => $validatedData['is_progressive'],
                'minimum_amount' => $validatedData['minimum_amount'],
            ]);

            if ($validatedData['is_progressive']) {
                $payroll_formula->brackets()->delete(); // Remove existing brackets
                foreach ($validatedData['brackets'] as $bracket) {
                    $payroll_formula->brackets()->create($bracket);
                }
            }

            return RequestResponse::ok('Payroll formula updated successfully.');
        });
    }

    public function destroy(Request $request)
    {
        $validatedData = $request->validate([
            'payroll_formula' => 'required|exists:payroll_formulas,slug',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $payroll_formula = PayrollFormula::findBySlug($validatedData['payroll_formula']);
            $payroll_formula->delete();

            return RequestResponse::ok('Payroll formula deleted successfully.');
        });
    }
}
