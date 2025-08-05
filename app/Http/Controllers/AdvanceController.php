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
        try {
            $business = Business::findBySlug(session('active_business_slug'));
            $advances = Advance::with('employee')
                ->whereHas('employee', function ($query) use ($business) {
                    $query->where('business_id', $business->id);
                })
                ->get();
            $advance_table = view('advances._table', compact('advances'))->render();
            return RequestResponse::ok('Ok', $advance_table);
        } catch (\Exception $e) {
            Log::error('Error fetching advances: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return RequestResponse::badRequest('Failed to fetch advances: ' . $e->getMessage());
        }
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
            $business = Business::findBySlug(session('active_business_slug'));
            $employee = Employee::where('id', $validatedData['employee_id'])
                ->where('business_id', $business->id)
                ->firstOrFail();

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

        $business = Business::findBySlug(session('active_business_slug'));
        $advance = Advance::where('id', $validatedData['advance'])
            ->whereHas('employee', function ($query) use ($business) {
                $query->where('business_id', $business->id);
            })
            ->firstOrFail();

        $employees = $business->employees;
        $advance_form = view('advances._form', compact('advance', 'employees'))->render();
        return RequestResponse::ok('Ok', $advance_form);
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'advance_id' => 'required|exists:advances,id',
            'employee_id' => 'required|exists:employees,id',
            'amount' => 'required|numeric|min:1',
            'date' => 'required|date',
            'note' => 'nullable|string',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $business = Business::findBySlug(session('active_business_slug'));
            $advance = Advance::where('id', $validatedData['advance_id'])
                ->whereHas('employee', function ($query) use ($business) {
                    $query->where('business_id', $business->id);
                })
                ->firstOrFail();

            $employee = Employee::where('id', $validatedData['employee_id'])
                ->where('business_id', $business->id)
                ->firstOrFail();

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
            $business = Business::findBySlug(session('active_business_slug'));
            $advance = Advance::where('id', $validatedData['advance_id'])
                ->whereHas('employee', function ($query) use ($business) {
                    $query->where('business_id', $business->id);
                })
                ->firstOrFail();

            $advance->delete();
            return RequestResponse::ok('Advance deleted successfully.');
        });
    }
}
