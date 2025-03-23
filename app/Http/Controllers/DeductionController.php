<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Deduction;
use Illuminate\Http\Request;
use App\Http\RequestResponse;
use App\Traits\HandleTransactions;
use Illuminate\Support\Facades\Log;

class DeductionController extends Controller
{
    use HandleTransactions;

    public function index(Request $request)
    {
        $page = 'Deductions';
        $description = 'Manage custom deductions for employees.';
        $business = Business::findBySlug(session('active_business_slug'));
        if (!$business) {
            return RequestResponse::badRequest('Business not found.');
        }

        $deductions = Deduction::where('business_id', $business->id)->get();

        return view('deductions.index', compact('page', 'description', 'deductions'));
    }

    public function fetch(Request $request)
    {
        try {
            $business = Business::findBySlug(session('active_business_slug'));
            if (!$business) {
                return RequestResponse::badRequest('Business not found.');
            }

            $deductions = Deduction::where('business_id', $business->id)->get();
            $deductionsTable = view('deductions._table', compact('deductions'))->render();

            return RequestResponse::ok('Deductions fetched successfully.', [
                'html' => $deductionsTable,
                'count' => $deductions->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch deductions:', ['error' => $e->getMessage()]);
            return RequestResponse::badRequest('Failed to fetch deductions.', [
                'errors' => [$e->getMessage()]
            ]);
        }
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'calculation_basis' => 'required|in:basic_pay,gross_pay,cash_pay,taxable_pay',
            'type' => 'required|in:fixed,rate',
            'amount' => [
                'nullable',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->type === 'fixed' && (is_null($value) || $value <= 0)) {
                        $fail('The amount field is required and must be greater than 0 when type is fixed.');
                    }
                },
            ],
            'rate' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->type === 'rate' && (is_null($value) || $value <= 0)) {
                        $fail('The rate field is required and must be greater than 0 when type is rate.');
                    }
                },
            ],
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $business = Business::findBySlug(session('active_business_slug'));
            if (!$business) {
                return RequestResponse::badRequest('Business not found.');
            }

            $slug = \Str::slug($validatedData['name']);
            $deduction = Deduction::create([
                'name' => $validatedData['name'],
                'slug' => $slug,
                'description' => $validatedData['description'],
                'calculation_basis' => $validatedData['calculation_basis'],
                'type' => $validatedData['type'],
                'amount' => $validatedData['type'] === 'fixed' ? $validatedData['amount'] : null,
                'rate' => $validatedData['type'] === 'rate' ? $validatedData['rate'] : null,
                'is_optional' => true,
                'business_id' => $business->id,
                'created_by' => auth()->id(),
            ]);

            return RequestResponse::created('Deduction created successfully.', $deduction->id);
        }, function ($e) {
            return RequestResponse::badRequest('Failed to create deduction.', [
                'errors' => $e instanceof \Illuminate\Validation\ValidationException
                    ? $e->errors()
                    : [$e->getMessage()]
            ]);
        });
    }

    public function edit(Request $request)
    {
        $validatedData = $request->validate([
            'deduction_id' => 'nullable|exists:deductions,id',
        ]);

        $business = Business::findBySlug(session('active_business_slug'));
        if (!$business) {
            return RequestResponse::badRequest('Business not found.');
        }

        $deduction = null;
        if (!empty($validatedData['deduction_id'])) {
            $deduction = Deduction::where('business_id', $business->id)
                ->where('id', $validatedData['deduction_id'])
                ->firstOrFail();
        }

        $form = view('deductions._form', compact('deduction'))->render();
        return RequestResponse::ok('Deduction form loaded successfully.', $form);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'deduction_id' => 'required|exists:deductions,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'calculation_basis' => 'required|in:basic_pay,gross_pay,cash_pay,taxable_pay',
            'type' => 'required|in:fixed,rate',
            'amount' => [
                'nullable',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->type === 'fixed' && (is_null($value) || $value <= 0)) {
                        $fail('The amount field is required and must be greater than 0 when type is fixed.');
                    }
                },
            ],
            'rate' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->type === 'rate' && (is_null($value) || $value <= 0)) {
                        $fail('The rate field is required and must be greater than 0 when type is rate.');
                    }
                },
            ],
        ]);

        return $this->handleTransaction(function () use ($validatedData, $id) {
            $business = Business::findBySlug(session('active_business_slug'));
            if (!$business) {
                return RequestResponse::badRequest('Business not found.');
            }

            $deduction = Deduction::where('business_id', $business->id)
                ->where('id', $id)
                ->firstOrFail();

            if ($deduction->id != $validatedData['deduction_id']) {
                return RequestResponse::badRequest('Deduction ID mismatch.');
            }

            $slug = \Str::slug($validatedData['name']);
            $deduction->update([
                'name' => $validatedData['name'],
                'slug' => $slug,
                'description' => $validatedData['description'],
                'calculation_basis' => $validatedData['calculation_basis'],
                'type' => $validatedData['type'],
                'amount' => $validatedData['type'] === 'fixed' ? $validatedData['amount'] : null,
                'rate' => $validatedData['type'] === 'rate' ? $validatedData['rate'] : null,
            ]);

            return RequestResponse::ok('Deduction updated successfully.');
        }, function ($e) {
            return RequestResponse::badRequest('Failed to update deduction.', [
                'errors' => $e instanceof \Illuminate\Validation\ValidationException
                    ? $e->errors()
                    : [$e->getMessage()]
            ]);
        });
    }

    public function destroy(Request $request, $id)
    {
        $validatedData = $request->validate([
            'deduction_id' => 'required|exists:deductions,id',
        ]);

        return $this->handleTransaction(function () use ($validatedData, $id) {
            $business = Business::findBySlug(session('active_business_slug'));
            if (!$business) {
                return RequestResponse::badRequest('Business not found.');
            }

            $deduction = Deduction::where('business_id', $business->id)
                ->where('id', $id)
                ->firstOrFail();

            if ($deduction->id != $validatedData['deduction_id']) {
                return RequestResponse::badRequest('Deduction ID mismatch.');
            }

            $deduction->delete();

            return RequestResponse::ok('Deduction deleted successfully.');
        });
    }
}
