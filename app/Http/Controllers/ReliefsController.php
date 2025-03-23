<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Relief;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Http\RequestResponse;
use App\Traits\HandleTransactions;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class ReliefsController extends Controller
{
    use HandleTransactions;

    public function index(Request $request)
    {
        $page = 'Reliefs';
        $description = 'Manage reliefs for payroll, applicable to employees.';
        $business = Business::findBySlug(session('active_business_slug'));
        if (!$business) {
            return RequestResponse::badRequest('Business not found.');
        }
        $reliefs = Relief::where('business_id', $business->id)->get();

        return view('reliefs.index', compact('page', 'description', 'reliefs'));
    }

    public function fetch(Request $request)
    {
        try {
            $business = Business::findBySlug(session('active_business_slug'));
            if (!$business) {
                return RequestResponse::badRequest('Business not found.');
            }
            $reliefs = Relief::where('business_id', $business->id)->get();

            $reliefsTable = view('reliefs._table', compact('reliefs'))->render();
            return RequestResponse::ok('Reliefs fetched successfully.', [
                'html' => $reliefsTable,
                'count' => $reliefs->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch reliefs:', ['error' => $e->getMessage()]);
            return RequestResponse::badRequest('Failed to fetch reliefs.', [
                'errors' => [$e->getMessage()]
            ]);
        }
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'type' => 'required|in:deductible_before_tax,deductible_after_tax',
            'name' => 'required|string|max:255',
            'greatest_or_least_of' => 'required|in:greatest,least',
            'fixed_amount' => 'nullable|numeric|min:0',
            'actual_amount' => 'nullable|boolean',
            'percentage_of_amount' => 'nullable|numeric|min:0|max:100',
            'percentage_of' => 'required_with:percentage_of_amount|in:total_salary,basic_salary,net_salary',
            'fraction_to_consider' => 'required|in:employee_only,employee_and_employer',
            'limit' => 'nullable|numeric|min:0',
            'round_off' => 'required|in:round_off_up,round_off_down',
            'decimal_places' => 'required|integer|min:0|max:5',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $business = Business::findBySlug(session('active_business_slug'));
            if (!$business) {
                return RequestResponse::badRequest('Business not found.');
            }

            $relief = Relief::create([
                'business_id' => $business->id,
                'type' => $validatedData['type'],
                'name' => $validatedData['name'],
                'slug' => Str::slug($validatedData['name']),
                'greatest_or_least_of' => $validatedData['greatest_or_least_of'],
                'amount' => $validatedData['fixed_amount'] ?? 0,
                'actual_amount' => $validatedData['actual_amount'] ?? false,
                'percentage_of_amount' => $validatedData['percentage_of_amount'] ?? null,
                'percentage_of' => $validatedData['percentage_of'] ?? null,
                'fraction_to_consider' => $validatedData['fraction_to_consider'],
                'limit' => $validatedData['limit'] ?? null,
                'round_off' => $validatedData['round_off'],
                'decimal_places' => $validatedData['decimal_places'],
                'is_active' => true,
            ]);

            return RequestResponse::created('Relief created successfully.', $relief->id);
        });
    }

    public function edit(Request $request)
    {
        $validatedData = $request->validate([
            'relief_id' => 'nullable|exists:reliefs,id',
        ]);

        $business = Business::findBySlug(session('active_business_slug'));
        if (!$business) {
            return RequestResponse::badRequest('Business not found.');
        }
        $relief = null;

        if (!empty($validatedData['relief_id'])) {
            $relief = Relief::where('business_id', $business->id)
                ->where('id', $validatedData['relief_id'])
                ->firstOrFail();
        }

        $form = view('reliefs._form', compact('relief'))->render();
        return RequestResponse::ok('Relief form loaded successfully.', $form);
    }

    public function show(Request $request)
    {
        $validatedData = $request->validate([
            'relief_id' => 'required|exists:reliefs,id',
        ]);

        $business = Business::findBySlug(session('active_business_slug'));
        if (!$business) {
            return RequestResponse::badRequest('Business not found.');
        }

        $relief = Relief::where('business_id', $business->id)
            ->where('id', $validatedData['relief_id'])
            ->firstOrFail();

        $modal = view('reliefs._modal', compact('relief'))->render();
        return RequestResponse::ok('Relief details loaded successfully.', $modal);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'relief_id' => 'required|exists:reliefs,id',
            'type' => 'required|in:deductible_before_tax,deductible_after_tax',
            'name' => 'required|string|max:255',
            'greatest_or_least_of' => 'required|in:greatest,least',
            'fixed_amount' => 'nullable|numeric|min:0',
            'actual_amount' => 'nullable|boolean',
            'percentage_of_amount' => 'nullable|numeric|min:0|max:100',
            'percentage_of' => 'required_with:percentage_of_amount|in:total_salary,basic_salary,net_salary',
            'fraction_to_consider' => 'required|in:employee_only,employee_and_employer',
            'limit' => 'nullable|numeric|min:0',
            'round_off' => 'required|in:round_off_up,round_off_down',
            'decimal_places' => 'required|integer|min:0|max:5',
        ]);

        return $this->handleTransaction(function () use ($validatedData, $id) {
            $business = Business::findBySlug(session('active_business_slug'));
            if (!$business) {
                return RequestResponse::badRequest('Business not found.');
            }

            $relief = Relief::where('business_id', $business->id)
                ->where('id', $id)
                ->firstOrFail();

            if ($relief->id != $validatedData['relief_id']) {
                return RequestResponse::badRequest('Relief ID mismatch.');
            }

            $relief->update([
                'type' => $validatedData['type'],
                'name' => $validatedData['name'],
                'slug' => Str::slug($validatedData['name']),
                'greatest_or_least_of' => $validatedData['greatest_or_least_of'],
                'amount' => $validatedData['fixed_amount'] ?? 0,
                'actual_amount' => $validatedData['actual_amount'] ?? false,
                'percentage_of_amount' => $validatedData['percentage_of_amount'] ?? null,
                'percentage_of' => $validatedData['percentage_of'] ?? null,
                'fraction_to_consider' => $validatedData['fraction_to_consider'],
                'limit' => $validatedData['limit'] ?? null,
                'round_off' => $validatedData['round_off'],
                'decimal_places' => $validatedData['decimal_places'],
            ]);

            return RequestResponse::ok('Relief updated successfully.');
        });
    }

    public function destroy(Request $request, $id)
    {
        $validatedData = $request->validate([
            'relief_id' => 'required|exists:reliefs,id',
        ]);

        return $this->handleTransaction(function () use ($validatedData, $id) {
            $business = Business::findBySlug(session('active_business_slug'));
            if (!$business) {
                return RequestResponse::badRequest('Business not found.');
            }

            $relief = Relief::where('business_id', $business->id)
                ->where('id', $id)
                ->firstOrFail();

            if ($relief->id != $validatedData['relief_id']) {
                return RequestResponse::badRequest('Relief ID mismatch.');
            }

            $relief->delete();

            return RequestResponse::ok('Relief deleted successfully.');
        });
    }
}