<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\PayrollFormula;
use App\Models\PayrollFormulaBracket;
use App\Models\Employee;
use App\Models\EmployeePayrollDetail;
use Illuminate\Http\Request;
use App\Http\RequestResponse;
use App\Traits\HandleTransactions;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class PayrollFormulaController extends Controller
{
    use HandleTransactions;

    public function index(Request $request)
    {
        $page = 'Payroll Formulas';
        $description = 'Manage payroll formulas and assign them to employees.';
        $business = Business::findBySlug(session('active_business_slug'));
        if (!$business) {
            return RequestResponse::badRequest('Business not found.');
        }

        $formulas = PayrollFormula::with('brackets')->where('business_id', $business->id)->get();
        $employees = Employee::where('business_id', $business->id)->with('payrollDetails')->get();

        return view('payroll-formulas.index', compact('page', 'description', 'formulas', 'employees'));
    }

    public function fetch(Request $request)
    {
        try {
            $business = Business::findBySlug(session('active_business_slug'));
            if (!$business) {
                return RequestResponse::badRequest('Business not found.');
            }

            $formulas = PayrollFormula::with('brackets')->where('business_id', $business->id)->get();
            $formulasTable = view('payroll-formulas._table', compact('formulas'))->render();

            return RequestResponse::ok('Payroll formulas fetched successfully.', [
                'html' => $formulasTable,
                'count' => $formulas->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch payroll formulas:', ['error' => $e->getMessage()]);
            return RequestResponse::badRequest('Failed to fetch payroll formulas.', [
                'errors' => [$e->getMessage()]
            ]);
        }
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'formula_type' => 'required|in:rate,amount,fixed',
            'calculation_basis' => 'required|in:basic_pay,gross_pay,taxable_pay',
            'is_progressive' => 'nullable|boolean',
            'minimum_amount' => 'nullable|numeric|min:0',
            'applies_to' => 'required|in:all,specific',
            'brackets' => 'required_if:is_progressive,1|array',
            'brackets.*.min' => 'nullable|numeric|min:0',
            'brackets.*.max' => 'nullable|numeric|min:0',
            'brackets.*.rate' => 'nullable|numeric|min:0',
            'brackets.*.amount' => 'nullable|numeric|min:0',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $business = Business::findBySlug(session('active_business_slug'));
            if (!$business) {
                return RequestResponse::badRequest('Business not found.');
            }

            $slug = \Str::slug($validatedData['name']);
            $formula = PayrollFormula::create([
                'name' => $validatedData['name'],
                'slug' => $slug,
                'formula_type' => $validatedData['formula_type'],
                'calculation_basis' => $validatedData['calculation_basis'],
                'is_progressive' => $validatedData['is_progressive'] ?? 0, // Default to 0 if not present
                'minimum_amount' => $validatedData['minimum_amount'] ?? null,
                'applies_to' => $validatedData['applies_to'],
                'business_id' => $business->id,
            ]);

            if (($validatedData['is_progressive'] ?? 0) && !empty($validatedData['brackets'])) {
                foreach ($validatedData['brackets'] as $bracket) {
                    PayrollFormulaBracket::create([
                        'payroll_formula_id' => $formula->id,
                        'min' => $bracket['min'] ?? null,
                        'max' => $bracket['max'] ?? null,
                        'rate' => $bracket['rate'] ?? null,
                        'amount' => $bracket['amount'] ?? null,
                    ]);
                }
            }

            return RequestResponse::created('Payroll formula created successfully.', $formula->id);
        });
    }

    public function edit(Request $request)
    {
        $validatedData = $request->validate([
            'formula_id' => 'nullable|exists:payroll_formulas,id',
        ]);

        $business = Business::findBySlug(session('active_business_slug'));
        if (!$business) {
            return RequestResponse::badRequest('Business not found.');
        }

        $formula = null;
        if (!empty($validatedData['formula_id'])) {
            $formula = PayrollFormula::with('brackets')
                ->where('business_id', $business->id)
                ->where('id', $validatedData['formula_id'])
                ->firstOrFail();
        }

        $form = view('payroll-formulas._form', compact('formula'))->render();
        return RequestResponse::ok('Payroll formula form loaded successfully.', $form);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'formula_id' => 'required|exists:payroll_formulas,id',
            'name' => 'required|string|max:255',
            'formula_type' => 'required|in:rate,amount,fixed',
            'calculation_basis' => 'required|in:basic_pay,gross_pay,taxable_pay',
            'is_progressive' => 'nullable|boolean',
            'minimum_amount' => 'nullable|numeric|min:0',
            'applies_to' => 'required|in:all,specific',
            'brackets' => 'required_if:is_progressive,1|array',
            'brackets.*.min' => 'nullable|numeric|min:0',
            'brackets.*.max' => 'nullable|numeric|min:0',
            'brackets.*.rate' => 'nullable|numeric|min:0',
            'brackets.*.amount' => 'nullable|numeric|min:0',
        ]);

        return $this->handleTransaction(function () use ($validatedData, $id) {
            $business = Business::findBySlug(session('active_business_slug'));
            if (!$business) {
                return RequestResponse::badRequest('Business not found.');
            }

            $formula = PayrollFormula::where('business_id', $business->id)
                ->where('id', $id)
                ->firstOrFail();

            if ($formula->id != $validatedData['formula_id']) {
                return RequestResponse::badRequest('Formula ID mismatch.');
            }

            $slug = \Str::slug($validatedData['name']);
            $formula->update([
                'name' => $validatedData['name'],
                'slug' => $slug,
                'formula_type' => $validatedData['formula_type'],
                'calculation_basis' => $validatedData['calculation_basis'],
                'is_progressive' => $validatedData['is_progressive'] ?? 0, // Default to 0 if not present
                'minimum_amount' => $validatedData['minimum_amount'] ?? null,
                'applies_to' => $validatedData['applies_to'],
            ]);

            if (($validatedData['is_progressive'] ?? 0)) {
                $formula->brackets()->delete();
                foreach ($validatedData['brackets'] as $bracket) {
                    PayrollFormulaBracket::create([
                        'payroll_formula_id' => $formula->id,
                        'min' => $bracket['min'] ?? null,
                        'max' => $bracket['max'] ?? null,
                        'rate' => $bracket['rate'] ?? null,
                        'amount' => $bracket['amount'] ?? null,
                    ]);
                }
            }

            return RequestResponse::ok('Payroll formula updated successfully.');
        });
    }

    public function destroy(Request $request, $id)
    {
        $validatedData = $request->validate([
            'formula_id' => 'required|exists:payroll_formulas,id',
        ]);

        return $this->handleTransaction(function () use ($validatedData, $id) {
            $business = Business::findBySlug(session('active_business_slug'));
            if (!$business) {
                return RequestResponse::badRequest('Business not found.');
            }

            $formula = PayrollFormula::where('business_id', $business->id)
                ->where('id', $id)
                ->firstOrFail();

            if ($formula->id != $validatedData['formula_id']) {
                return RequestResponse::badRequest('Formula ID mismatch.');
            }

            $formula->brackets()->delete();
            $formula->delete();

            return RequestResponse::ok('Payroll formula deleted successfully.');
        });
    }

    public function subscribe(Request $request)
    {
        $validatedData = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'formula_id' => 'required|exists:payroll_formulas,id',
            'subscribe' => 'required|boolean',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $business = Business::findBySlug(session('active_business_slug'));
            if (!$business) {
                return RequestResponse::badRequest('Business not found.');
            }

            $employee = Employee::where('business_id', $business->id)
                ->where('id', $validatedData['employee_id'])
                ->firstOrFail();
            $formula = PayrollFormula::where('business_id', $business->id)
                ->where('id', $validatedData['formula_id'])
                ->firstOrFail();

            $payrollDetail = EmployeePayrollDetail::firstOrCreate(
                ['employee_id' => $employee->id],
                ['business_id' => $business->id]
            );

            if ($formula->slug === 'helb') {
                $payrollDetail->update(['has_helb' => $validatedData['subscribe']]);
            }

            return RequestResponse::ok($validatedData['subscribe'] ? 'Employee subscribed to formula.' : 'Employee unsubscribed from formula.');
        });
    }
}
