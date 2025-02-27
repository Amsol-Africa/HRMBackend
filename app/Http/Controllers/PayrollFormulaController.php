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

    public function create(Request $request)
    {
        $business = Business::findBySlug(session('active_business_slug'));

        $business_formulas = $business->payrollFormulas()->with('brackets')->get();

        // Fetch system-wide formulas
        $system_payroll_formulas = PayrollFormula::with('brackets')
            ->whereNull('business_id')
            ->whereNotIn('name', $business_formulas->pluck('name'))
            ->get();

        $payroll_formulas = $business_formulas->concat($system_payroll_formulas);

        $formulaCreate = view('payroll._create_formulas', compact('payroll_formulas'))->render();

        return RequestResponse::ok('Ok', $formulaCreate);
    }

    public function store(Request $request)
    {
        Log::debug($request->all());
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:payroll_formulas,formula_name',
            'calculation_basis' => 'required|in:basic pay,gross pay,cash pay,taxable pay',
            'is_progressive' => 'required|boolean',
            'minimum_amount' => 'nullable|numeric|min:0',
            'min.*' => 'required_if:is_progressive,1|numeric|min:0',
            'max.*' => 'required_if:is_progressive,1|numeric|gt:min.*',
            'rate.*' => 'required_if:is_progressive,1|nullable|numeric|min:0',
            'amount.*' => 'required_if:is_progressive,1|nullable|numeric|min:0',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $payroll_formula = PayrollFormula::create([
                'formula_name' => $validatedData['name'],
                'calculation_basis' => $validatedData['calculation_basis'],
                'is_progressive' => $validatedData['is_progressive'],
                'minimum_amount' => $validatedData['minimum_amount'],
            ]);

            if ($validatedData['is_progressive']) {
                $minValues = $validatedData['min'];
                $maxValues = $validatedData['max'];
                $rateValues = $validatedData['rate'];
                $amountValues = $validatedData['amount'];

                if (is_array($minValues)) {
                    foreach ($minValues as $index => $minValue) {
                        $payroll_formula->brackets()->create([
                            'min_value' => $minValue,
                            'max_value' => $maxValues[$index],
                            'rate' => isset($rateValues[$index]) ? $rateValues[$index] : null,
                            'amount' => isset($amountValues[$index]) ? $amountValues[$index] : null,
                        ]);
                    }
                }
            }

            return RequestResponse::ok('Payroll formula created successfully.');
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
        Log::debug($request->all());

        $validatedData = $request->validate([
            'payroll_formula_slug' => 'required|exists:payroll_formulas,slug',
            'name' => 'required|string|max:255',
            'calculation_basis' => 'required|in:basic_pay,gross_pay,cash_pay,taxable_pay',
            'is_progressive' => 'required|boolean',
            'minimum_amount' => 'nullable|numeric|min:0',
            'min.*' => 'required_if:is_progressive,true|numeric|min:0',
            'max.*' => 'required_if:is_progressive,true|numeric|gt:min.*',
            'rate.*' => 'nullable|required_if:is_progressive,true|numeric|min:0|max:100',
            'amount.*' => 'nullable|required_if:is_progressive,true|numeric|min:0',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $business = Business::findBySlug(session('active_business_slug'));
            $payroll_formula = PayrollFormula::where('slug', $validatedData['payroll_formula_slug'])->firstOrFail();

            if ($business) {
                $existingFormula = PayrollFormula::where('business_id', $business->id)
                    ->where('name', $validatedData['name'])
                    ->first();

                if ($existingFormula) {
                    $payroll_formula = $existingFormula;
                } else {
                    $payroll_formula = $business->payrollFormulas()->create([
                        'name' => $validatedData['name'],
                        'calculation_basis' => $validatedData['calculation_basis'],
                        'is_progressive' => $validatedData['is_progressive'],
                        'minimum_amount' => $validatedData['minimum_amount'],
                    ]);
                }
            } else {
                $payroll_formula->update([
                    'name' => $validatedData['name'],
                    'calculation_basis' => $validatedData['calculation_basis'],
                    'is_progressive' => $validatedData['is_progressive'],
                    'minimum_amount' => $validatedData['minimum_amount'],
                ]);
            }

            if ($validatedData['is_progressive']) {
                $payroll_formula->brackets()->delete();

                $minValues = $validatedData['min'];
                $maxValues = $validatedData['max'];
                $rateValues = $validatedData['rate'];
                $amountValues = $validatedData['amount'];

                foreach ($minValues as $index => $minValue) {
                    $payroll_formula->brackets()->create([
                        'min' => $minValue,
                        'max' => $maxValues[$index] ?? null,
                        'rate' => $rateValues[$index] ?? null,
                        'amount' => $amountValues[$index] ?? null,
                    ]);
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
