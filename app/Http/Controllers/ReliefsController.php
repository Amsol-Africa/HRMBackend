<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Relief;
use Illuminate\Http\Request;
use App\Http\RequestResponse;
use App\Traits\HandleTransactions;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ReliefsController extends Controller
{
    use HandleTransactions;

    public function index()
    {
        $business = $this->getActiveBusiness();
        if (!$business) return RequestResponse::badRequest('Business not found.');

        $reliefs = Relief::where('business_id', $business->id)->get();
        return view('reliefs.index', [
            'page' => 'Reliefs',
            'description' => 'Manage payroll reliefs applicable to employees.',
            'reliefs' => $reliefs
        ]);
    }

    public function fetch(Request $request)
    {
        try {
            $business = $this->getActiveBusiness();
            if (!$business) return RequestResponse::badRequest('Business not found.');

            $reliefs = Relief::where('business_id', $business->id)->get();
            $reliefsTable = view('reliefs._table', compact('reliefs'))->render();
            return RequestResponse::ok('Reliefs fetched successfully.', [
                'html' => $reliefsTable,
                'count' => $reliefs->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch reliefs:', ['error' => $e->getMessage()]);
            return RequestResponse::badRequest('Failed to fetch reliefs.', ['errors' => [$e->getMessage()]]);
        }
    }

    public function store(Request $request)
    {
        $business = $this->getActiveBusiness();
        if (!$business) return RequestResponse::badRequest('Business not found.');

        $validatedData = $this->validateRelief($request);
        return $this->handleTransaction(function () use ($business, $validatedData) {
            $relief = Relief::create([
                'business_id' => $business->id,
                'name' => $validatedData['name'],
                'slug' => Str::slug($validatedData['name']),
                'description' => $validatedData['description'],
                'computation_method' => $validatedData['computation_method'],
                'amount' => $validatedData['amount'] ?? 0,
                'percentage_of_amount' => $validatedData['percentage_of_amount'] ?? null,
                'percentage_of' => $validatedData['percentage_of'] ?? null,
                'limit' => $validatedData['limit'] ?? null,
                'is_active' => true,
            ]);
            return RequestResponse::created('Relief created successfully.', $relief->id);
        });
    }

    public function edit(Request $request, $slug)
    {
        $business = $this->getActiveBusiness();
        if (!$business) return RequestResponse::badRequest('Business not found.');

        $relief = Relief::where('business_id', $business->id)->where('slug', $slug)->firstOrFail();
        $form = view('reliefs._form', compact('relief'))->render();
        return RequestResponse::ok('Relief form loaded successfully.', $form);
    }

    public function show(Request $request, $slug)
    {
        $business = $this->getActiveBusiness();
        if (!$business) return RequestResponse::badRequest('Business not found.');

        $relief = Relief::where('business_id', $business->id)->where('slug', $slug)->firstOrFail();
        $modal = view('reliefs._modal', compact('relief'))->render();
        return RequestResponse::ok('Relief details loaded successfully.', $modal);
    }

    public function update(Request $request, $slug)
    {
        $business = $this->getActiveBusiness();
        if (!$business) return RequestResponse::badRequest('Business not found.');

        $relief = Relief::where('business_id', $business->id)->where('slug', $slug)->firstOrFail();
        $validatedData = $this->validateRelief($request);

        return $this->handleTransaction(function () use ($relief, $validatedData) {
            $relief->update([
                'name' => $validatedData['name'],
                'slug' => Str::slug($validatedData['name']),
                'description' => $validatedData['description'],
                'computation_method' => $validatedData['computation_method'],
                'amount' => $validatedData['amount'] ?? 0,
                'percentage_of_amount' => $validatedData['percentage_of_amount'] ?? null,
                'percentage_of' => $validatedData['percentage_of'] ?? null,
                'limit' => $validatedData['limit'] ?? null,
            ]);
            return RequestResponse::ok('Relief updated successfully.');
        });
    }

    public function destroy(Request $request, $slug)
    {
        $business = $this->getActiveBusiness();
        if (!$business) return RequestResponse::badRequest('Business not found.');

        $relief = Relief::where('business_id', $business->id)->where('slug', $slug)->firstOrFail();
        return $this->handleTransaction(function () use ($relief) {
            $relief->delete();
            return RequestResponse::ok('Relief deleted successfully.');
        });
    }

    private function validateRelief(Request $request)
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'computation_method' => 'required|in:fixed,percentage',
            'amount' => 'nullable|numeric|min:0',
            'percentage_of_amount' => 'nullable|numeric|min:0|max:100',
            'percentage_of' => 'nullable|in:total_salary,basic_salary,net_salary',
            'limit' => 'nullable|numeric|min:0',
        ]);
    }

    private function getActiveBusiness()
    {
        return Business::findBySlug(session('active_business_slug'));
    }
}