<?php

namespace App\Http\Controllers;

use App\Enum\Status;
use App\Models\Business;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Http\RequestResponse;
use App\Traits\HandleTransactions;
use Illuminate\Support\Facades\Log;

class DepartmentController extends Controller
{
    use HandleTransactions;
    public function fetch(Request $request)
    {
        $user = $request->user();
        $business = $user->business;

        $departments = Department::where('business_id', $business->id)->get();
        $department_cards = view('departments._cards', compact('departments'))->render();
        return RequestResponse::ok('Ok', $department_cards);
    }
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'department_name' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        return $this->handleTransaction(function () use ($request, $validatedData) {
            $user = auth()->user();
            $business = Business::findBySlug(session('active_business_slug'));

            $department = $business->departments()->create([
                'name' => $validatedData['department_name'],
                'description' => $validatedData['description'] ?? null,
            ]);

            $department->setStatus(Status::ACTIVE);

            return RequestResponse::created('Department Added successfully.');
        });
    }
    public function edit(Request $request)
    {
        $validatedData = $request->validate([
            'department' => 'required|string|exists:departments,slug',
        ]);

        $department = Department::findBySlug($validatedData['department']);
        $department_form = view('departments._form', compact('department'))->render();
        return RequestResponse::ok('Ok', $department_form);
    }
    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'department_slug' => 'required|exists:departments,slug',
            'department_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        return $this->handleTransaction(function () use ($request, $validatedData) {

            $department = Department::findBySlug($validatedData['department_slug']);

            $department->update([
                'name' => $validatedData['department_name'],
                'description' => $validatedData['description'] ?? null,
            ]);

            $department->setStatus(Status::ACTIVE);

            return RequestResponse::ok('Department Updated successfully.');
        });
    }
    public function destroy(Request $request)
    {
        $validatedData = $request->validate([
            'department' => 'required|exists:departments,slug',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {

            $department = Department::findBySlug($validatedData['department']);

            if ($department) {
                $department->delete();
                return RequestResponse::ok('Department deleted successfully.');
            }

            return RequestResponse::badRequest('Failed to delete department.', 404);
        });
    }

}
