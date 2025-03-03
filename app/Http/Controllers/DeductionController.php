<?php

namespace App\Http\Controllers;

use App\Enum\Status;
use App\Models\Business;
use App\Models\Location;
use App\Models\Deduction;
use Illuminate\Http\Request;
use App\Http\RequestResponse;
use App\Traits\HandleTransactions;

class DeductionController extends Controller
{
    use HandleTransactions;

    public function fetch(Request $request)
    {
        $business = Business::findBySlug(session('active_business_slug'));
        $deductions = $business->deductions;
        $deduction_table = view('deductions._table', compact('deductions'))->render();
        return RequestResponse::ok('Ok', $deduction_table);
    }

    public function store(Request $request)
    {
        return $this->handleTransaction(function () use ($request) {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'location' => 'nullable|exists:locations,slug',
                'calculation_basis' => 'required|string',
            ]);

            $business = Business::findBySlug(session('active_business_slug'));
            if (!$business) {
                return RequestResponse::badRequest('Invalid business.');
            }

            $location = $request->location ? Location::findBySlug($request->location) : null;

            $deduction = Deduction::create([
                'name' => $request->name,
                'created_by' => $request->user()->id,
                'description' => $request->description,
                'business_id' => $business->id,
                'location_id' => $location?->id,
                'calculation_basis' => $request->calculation_basis ?? null,
            ])->setStatus(Status::ACTIVE);

            return RequestResponse::created('Deduction created successfully.');
        });
    }

    public function edit(Request $request)
    {
        $validatedData = $request->validate([
            'deduction' => 'required|exists:deductions,id',
        ]);

        $deduction = Deduction::findOrFail($validatedData['deduction']);
        $business = Business::findBySlug(session('active_business_slug'));
        $employees = $business->employees;
        $deduction_form = view('deductions._form', compact('deduction', 'employees'))->render();
        return RequestResponse::ok('Ok', $deduction_form);
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'deduction_id' => 'required|exists:deductions,id',
            'amount' => 'required|numeric|min:1',
            'date' => 'required|date',
            'note' => 'nullable|string',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $deduction = Deduction::findOrFail($validatedData['deduction_id']);
            $deduction->update($validatedData);
            return RequestResponse::ok('Deduction updated successfully.');
        });
    }

    public function destroy(Request $request)
    {
        $validatedData = $request->validate([
            'deduction_id' => 'required|exists:deductions,id',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $deduction = Deduction::findOrFail($validatedData['deduction_id']);
            $deduction->delete();
            return RequestResponse::ok('Deduction deleted successfully.');
        });
    }
}
