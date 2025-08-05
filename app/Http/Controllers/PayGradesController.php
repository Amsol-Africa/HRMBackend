<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\PayGrade;
use App\Models\JobCategory;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Http\RequestResponse;
use App\Traits\HandleTransactions;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class PayGradesController extends Controller
{
    use HandleTransactions;

    public function index(Request $request)
    {
        $page = 'Pay Grades';
        $description = 'Manage pay grades for employees, linking to job categories or departments.';
        $business = Business::findBySlug(session('active_business_slug'));
        if (!$business) {
            return RequestResponse::badRequest('Business not found.');
        }
        $payGrades = PayGrade::where('business_id', $business->id)
            ->with('jobCategory', 'department')
            ->get();
        $jobCategories = JobCategory::where('business_id', $business->id)->get();
        $departments = Department::where('business_id', $business->id)->get();

        return view('pay-grades.index', compact('page', 'description', 'payGrades', 'jobCategories', 'departments'));
    }

    public function fetch(Request $request)
    {
        try {
            $business = Business::findBySlug(session('active_business_slug'));
            if (!$business) {
                return RequestResponse::badRequest('Business not found.');
            }
            $payGrades = PayGrade::where('business_id', $business->id)
                ->with('jobCategory', 'department')
                ->get();

            $payGradesTable = view('pay-grades._table', compact('payGrades'))->render();
            return RequestResponse::ok('Pay grades fetched successfully.', [
                'html' => $payGradesTable,
                'count' => $payGrades->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch pay grades:', ['error' => $e->getMessage()]);
            return RequestResponse::badRequest('Failed to fetch pay grades.', [
                'errors' => [$e->getMessage()]
            ]);
        }
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'job_category_id' => 'nullable|exists:job_categories,id',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $business = Business::findBySlug(session('active_business_slug'));
            if (!$business) {
                return RequestResponse::badRequest('Business not found.');
            }

            $payGrade = PayGrade::create([
                'name' => $validatedData['name'],
                'amount' => $validatedData['amount'],
                'job_category_id' => $validatedData['job_category_id'] ?? null,
                'department_id' => $validatedData['department_id'] ?? null,
                'business_id' => $business->id,
            ]);

            return RequestResponse::created('Pay grade created successfully.', $payGrade->id);
        });
    }

    public function edit(Request $request)
    {
        $validatedData = $request->validate([
            'pay_grade_id' => 'nullable|exists:pay_grades,id',
        ]);

        $business = Business::findBySlug(session('active_business_slug'));
        if (!$business) {
            return RequestResponse::badRequest('Business not found.');
        }
        $jobCategories = JobCategory::where('business_id', $business->id)->get();
        $departments = Department::where('business_id', $business->id)->get();
        $payGrade = null;

        if (!empty($validatedData['pay_grade_id'])) {
            $payGrade = PayGrade::where('business_id', $business->id)
                ->where('id', $validatedData['pay_grade_id'])
                ->firstOrFail();
        }

        $form = view('pay-grades._form', compact('payGrade', 'jobCategories', 'departments'))->render();
        return RequestResponse::ok('Pay grade form loaded successfully.', $form);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'pay_grade_id' => 'required|exists:pay_grades,id',
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'job_category_id' => 'nullable|exists:job_categories,id',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        return $this->handleTransaction(function () use ($validatedData, $id) {
            $business = Business::findBySlug(session('active_business_slug'));
            if (!$business) {
                return RequestResponse::badRequest('Business not found.');
            }

            $payGrade = PayGrade::where('business_id', $business->id)
                ->where('id', $id)
                ->firstOrFail();

            if ($payGrade->id != $validatedData['pay_grade_id']) {
                return RequestResponse::badRequest('Pay grade ID mismatch.');
            }

            $payGrade->update([
                'name' => $validatedData['name'],
                'amount' => $validatedData['amount'],
                'job_category_id' => $validatedData['job_category_id'] ?? null,
                'department_id' => $validatedData['department_id'] ?? null,
            ]);

            return RequestResponse::ok('Pay grade updated successfully.');
        });
    }

    public function destroy(Request $request, $id)
    {
        $validatedData = $request->validate([
            'pay_grade_id' => 'required|exists:pay_grades,id',
        ]);

        return $this->handleTransaction(function () use ($validatedData, $id) {
            $business = Business::findBySlug(session('active_business_slug'));
            if (!$business) {
                return RequestResponse::badRequest('Business not found.');
            }

            $payGrade = PayGrade::where('business_id', $business->id)
                ->where('id', $id)
                ->firstOrFail();

            if ($payGrade->id != $validatedData['pay_grade_id']) {
                return RequestResponse::badRequest('Pay grade ID mismatch.');
            }

            $payGrade->delete();

            return RequestResponse::ok('Pay grade deleted successfully.');
        });
    }
}
