<?php

namespace App\Http\Controllers;

use App\Enum\Status;
use App\Models\Advance;
use App\Models\Business;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Http\RequestResponse;
use App\Traits\HandleTransactions;
use Illuminate\Support\Facades\Log;

class AdvanceController extends Controller
{
    use HandleTransactions;

    public function fetch(Request $request)
    {
        $advances = Advance::with('employee')->get();
        $advance_table = view('advances._table', compact('advances'))->render();
        return RequestResponse::ok('Ok', $advance_table);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'amount' => 'required|numeric|min:1',
            'date' => 'required|date',
            'note' => 'nullable|string',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $advance = Advance::create($validatedData);
            $advance->setStatus(Status::ACTIVE);
            return RequestResponse::created('Advance recorded successfully.');
        });
    }

    public function edit(Request $request)
    {
        $validatedData = $request->validate([
            'advance' => 'required|exists:advances,id',
        ]);

        $advance = Advance::findOrFail($validatedData['advance']);
        $business = Business::findBySlug(session('active_business_slug'));
        $employees = $business->employees;
        $advance_form = view('advances._form', compact('advance', 'employees'))->render();
        return RequestResponse::ok('Ok', $advance_form);
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'advance_id' => 'required|exists:advances,id',
            'amount' => 'required|numeric|min:1',
            'date' => 'required|date',
            'note' => 'nullable|string',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $advance = Advance::findOrFail($validatedData['advance_id']);
            $advance->update($validatedData);
            return RequestResponse::ok('Advance updated successfully.');
        });
    }

    public function destroy(Request $request)
    {
        $validatedData = $request->validate([
            'advance_id' => 'required|exists:advances,id',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $advance = Advance::findOrFail($validatedData['advance_id']);
            $advance->delete();
            return RequestResponse::ok('Advance deleted successfully.');
        });
    }
}
