<?php

namespace App\Http\Controllers;

use App\Enum\Status;
use App\Models\Loan;
use App\Models\Business;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Http\RequestResponse;
use App\Traits\HandleTransactions;

class LoanController extends Controller
{
    use HandleTransactions;

    public function fetch(Request $request)
    {
        $loans = Loan::with('employee')->get();
        $loan_table = view('loans._table', compact('loans'))->render();
        return RequestResponse::ok('Ok', $loan_table);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'amount' => 'required|numeric|min:1',
            'interest_rate' => 'nullable|numeric|min:0',
            'term_months' => 'nullable|integer|min:1',
            'start_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $loan = Loan::create($validatedData);
            $loan->setStatus(Status::ACTIVE);
            return RequestResponse::created('Loan recorded successfully.');
        });
    }

    public function edit(Request $request)
    {
        $validatedData = $request->validate([
            'loan' => 'required|exists:loans,id',
        ]);

        $loan = Loan::findOrFail($validatedData['loan']);
        $business = Business::findBySlug(session('active_business_slug'));
        $employees = $business->employees;
        $loan_form = view('loans._form', compact('loan', 'employees'))->render();
        return RequestResponse::ok('Ok', $loan_form);
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'loan_id' => 'required|exists:loans,id',
            'amount' => 'required|numeric|min:1',
            'interest_rate' => 'nullable|numeric|min:0',
            'term_months' => 'nullable|integer|min:1',
            'start_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $loan = Loan::findOrFail($validatedData['loan_id']);
            $loan->update($validatedData);
            return RequestResponse::ok('Loan updated successfully.');
        });
    }

    public function destroy(Request $request)
    {
        $validatedData = $request->validate([
            'loan_id' => 'required|exists:loans,id',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $loan = Loan::findOrFail($validatedData['loan_id']);
            $loan->delete();
            return RequestResponse::ok('Loan deleted successfully.');
        });
    }
}
