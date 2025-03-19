<?php

namespace App\Http\Controllers;

use App\Enum\Status;
use App\Models\Business;
use App\Models\Location;
use App\Models\Allowance;
use Illuminate\Http\Request;
use App\Http\RequestResponse;
use App\Traits\HandleTransactions;

class AllowanceController extends Controller
{
    use HandleTransactions;

    public function fetch(Request $request)
    {
        $business = Business::findBySlug(session('active_business_slug'));
        $allowances = $business->allowances;
        $allowance_table = view('allowances._table', compact('allowances'))->render();
        return RequestResponse::ok('Ok', $allowance_table);
    }

    public function store(Request $request)
    {
        return $this->handleTransaction(function () use ($request) {
            $request->validate([
                'name' => 'required|string|max:255',
                'is_taxable' => 'nullable|boolean',
                'location' => 'nullable|exists:locations,slug',
            ]);

            $business = Business::findBySlug(session('active_business_slug'));
            if (!$business) {
                return RequestResponse::badRequest('Invalid business.');
            }

            $location = $request->location ? Location::findBySlug($request->location) : null;

            $allowance = Allowance::create([
                'name' => $request->name,
                'business_id' => $business->id,
                'location_id' => $location?->id,
                'is_taxable' => $request->is_taxable,
            ])->setStatus(Status::ACTIVE);

            return RequestResponse::created('Allowance created successfully.');
        });
    }

    public function edit(Request $request)
    {
        $validatedData = $request->validate([
            'allowance' => 'required|exists:allowances,slug',
        ]);

        $allowance = Allowance::finBySlug($validatedData['allowance']);
        $business = Business::findBySlug(session('active_business_slug'));
        $allowance_form = view('allowances._form', compact('allowance'))->render();
        return RequestResponse::ok('Ok', $allowance_form);
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'allowance_slug' => 'required|exists:allowances,slug',
            'name' => 'required|string|max:255',
            'is_taxable' => 'nullable|boolean',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $allowance = Allowance::findBySlug($validatedData['allowance_slug']);
            $allowance->update($validatedData);
            return RequestResponse::ok('Allowance updated successfully.');
        });
    }

    public function destroy(Request $request)
    {
        $validatedData = $request->validate([
            'allowance_slug' => 'required|exists:allowances,slug',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $allowance = Allowance::findBySlug($validatedData['allowance_slug']);
            $allowance->delete();
            return RequestResponse::ok('Allowance deleted successfully.');
        });
    }
}