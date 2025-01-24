<?php

namespace App\Http\Controllers;

use App\Enum\Status;
use App\Models\Business;
use Illuminate\Http\Request;
use App\Http\RequestResponse;
use App\Traits\HandleTransactions;

class LeavePeriodController extends Controller
{
    use HandleTransactions;

    public function fetch(Request $request)
    {
        $business = Business::findBySlug(session('active_business_slug'));
        $leavePeriods = $business->leavePeriods()->get();
        $leavePeriodTable = view('leave._leave_periods_table', compact('leavePeriods'))->render();
        return RequestResponse::ok('Leave periods fetched successfully.', $leavePeriodTable);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'is_active' => 'nullable|boolean',
            'accept_applications' => 'nullable|boolean',
            'can_accrue' => 'nullable|boolean',
            'restrict_applications_within_dates' => 'nullable|boolean',
            'archive' => 'nullable|boolean',
            'autocreate' => 'nullable|boolean',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $business = Business::findBySlug(session('active_business_slug'));

            $leavePeriod = $business->leavePeriods()->create([
                'name' => $validatedData['name'],
                'start_date' => $validatedData['start_date'],
                'end_date' => $validatedData['end_date'],
                'accept_applications' => $validatedData['accept_applications'] ?? true,
                'can_accrue' => $validatedData['can_accrue'] ?? true,
                'restrict_applications_within_dates' => $validatedData['restrict_applications_within_dates'] ?? false,
                'archive' => $validatedData['archive'] ?? false,
                'autocreate' => $validatedData['autocreate'] ?? false,
            ])->setStatus(Status::ACTIVE);

            return RequestResponse::created('Leave period created successfully.');
        });
    }

    public function showDetails($leavePeriodId)
    {
        $business = Business::findBySlug(session('active_business_slug'));
        $leavePeriod = $business->leavePeriods()->findOrFail($leavePeriodId);

        $detailsView = view('leave._leave_period_details', compact('leavePeriod'))->render();

        return RequestResponse::ok('Leave period details fetched successfully.', $detailsView);
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'leave_period_slug' => 'required|string|exists:leave_periods,slug',
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'is_active' => 'nullable|boolean',
            'accept_applications' => 'nullable|boolean',
            'can_accrue' => 'nullable|boolean',
            'restrict_applications_within_dates' => 'nullable|boolean',
            'archive' => 'nullable|boolean',
            'autocreate' => 'nullable|boolean',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $business = Business::findBySlug(session('active_business_slug'));
            $leavePeriod = $business->leavePeriods()->findBySlug($validatedData['leave_period_slug']);

            $leavePeriod->update([
                'name' => $validatedData['name'],
                'start_date' => $validatedData['start_date'],
                'end_date' => $validatedData['end_date'],
                'is_active' => $validatedData['is_active'] ?? $leavePeriod->is_active,
                'accept_applications' => $validatedData['accept_applications'] ?? $leavePeriod->accept_applications,
                'can_accrue' => $validatedData['can_accrue'] ?? $leavePeriod->can_accrue,
                'restrict_applications_within_dates' => $validatedData['restrict_applications_within_dates'] ?? $leavePeriod->restrict_applications_within_dates,
                'archive' => $validatedData['archive'] ?? $leavePeriod->archive,
                'autocreate' => $validatedData['autocreate'] ?? $leavePeriod->autocreate,
            ]);

            return RequestResponse::ok('Leave period updated successfully.');
        });
    }

    public function destroy(Request $request)
    {
        $validatedData = $request->validate([
            'leave_period_slug' => 'required|string|exists:leave_periods,slug',
        ]);
        return $this->handleTransaction(function () use ($validatedData) {
            $business = Business::findBySlug(session('active_business_slug'));
            $leavePeriod = $business->leavePeriods()->findBySlug($validatedData['leave_period_slug']);

            $leavePeriod->delete();

            return RequestResponse::ok('Leave period deleted successfully.');
        });
    }
}
