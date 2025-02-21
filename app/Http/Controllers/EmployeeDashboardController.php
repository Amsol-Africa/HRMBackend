<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Business;
use App\Models\Attendance;
use App\Models\Payslip;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class EmployeeDashboardController extends Controller
{
    // Dashboard Overview
    public function index(Request $request)
    {
        $page = "Dashboard";
        $employee = Auth::user();
        $leave_count = LeaveRequest::where('employee_id', $employee->id)->count();
        $pending_leaves = LeaveRequest::where('employee_id', $employee->id)->where('approved_by', 'pending')->count();

        // $work_days = Attendance::where('employee_id', $employee->id)->count();
        // $payslips = Payslip::where('employee_id', $employee->id)->count();

        return view('employee.index', compact('page', 'leave_count', 'pending_leaves'));
    }

    // Profile Management
    public function profile()
    {
        $page = "Profile";
        return view('employee.profile', compact('page'));
    }

    public function updateProfile(Request $request)
    {
        $employee = Auth::user();
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $employee->id,
            'phone' => 'nullable|string|max:15',
        ]);

        $employee->update($request->only(['name', 'email', 'phone']));
        return back()->with('success', 'Profile updated successfully!');
    }
    // Leave Requests
    public function requestLeave()
    {
        $page = "Request Leave";
        $description = "";

        $business = Business::findBySlug(session('active_business_slug'));
        $leaveTypes = $business->leaveTypes;

        // Fetch all leave requests for the logged-in user
        $leaveRequests = LeaveRequest::where('employee_id', Auth::id())->latest()->get();

        return view('leave.request-leave', compact('page', 'description', 'leaveTypes', 'leaveRequests'));
    }

    public function viewLeaves()
    {
        $page = "My Leaves";
        $leaves = LeaveRequest::where('employee_id', Auth::id())->latest()->get();
        return view('leave.index', compact('page', 'leaves'));
    }

    // Employee Sign-in (Attendance)
    public function checkIn()
    {
        $employee = Auth::user();

        $existingCheckIn = Attendance::where('employee_id', $employee->id)
            ->whereDate('created_at', Carbon::today())
            ->first();

        if ($existingCheckIn) {
            return back()->with('error', 'You have already checked in today!');
        }

        Attendance::create([
            'employee_id' => $employee->id,
            'check_in' => Carbon::now(),
        ]);

        return back()->with('success', 'Successfully checked in!');
    }

    // Download P9 Form
    public function downloadP9()
    {
        $employee = Auth::user();
        $p9Path = storage_path("app/p9_forms/{$employee->id}_p9.pdf");

        if (!file_exists($p9Path)) {
            return back()->with('error', 'P9 form not found!');
        }

        return response()->download($p9Path);
    }

    // View Payment Slips
    public function viewPayslips()
    {
        $page = "Payslips";
        $payslips = Payslip::where('employee_id', Auth::id())->latest()->get();
        return view('employee.payslips', compact('page', 'payslips'));
    }

    public function downloadPayslip($id)
    {
        $payslip = Payslip::where('id', $id)->where('employee_id', Auth::id())->first();

        if (!$payslip) {
            return back()->with('error', 'Payslip not found!');
        }

        $filePath = storage_path("app/payslips/{$payslip->file}");

        if (!file_exists($filePath)) {
            return back()->with('error', 'Payslip file not found!');
        }

        return response()->download($filePath);
    }
}