<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Payslip;
use App\Models\Business;
use App\Models\Employee;
use App\Models\LeaveType;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
        return view('employee.index', compact('page', 'leave_count', 'pending_leaves'));
    }
    // Leave Requests
    public function requestLeave()
    {
        $page = "Request Leave";
        $description = "";
        $business = Business::findBySlug(session('active_business_slug'));
        $leaveTypes = $business->leaveTypes;
        $leaveRequests = LeaveRequest::where('employee_id', Auth::id())->latest()->get();

        return view('leave.request-leave', compact('page', 'description', 'leaveTypes', 'leaveRequests'));
    }

    public function viewLeaves()
    {
        $page = "My Leaves";
        $leaves = LeaveRequest::where('employee_id', Auth::id())->latest()->get();
        return view('leave.index', compact('page', 'leaves'));
    }

    public function leaveApplication(Request $request, String $business_slug, String $reference_number)
    {
        $business = Business::findBySlug($business_slug);
        $leaveRequest = LeaveRequest::where('reference_number', $reference_number)->where('business_id', $business->id)->first();
        $page = 'Leave - #' . $reference_number;
        $description = '';

        $statusHistory = $leaveRequest->statuses()->orderBy('created_at')->get();

        $timelineItem = [
            'reference_number' => $leaveRequest->reference_number,
            'employee_name' => $leaveRequest->employee->user->name,
            'leave_type' => $leaveRequest->leaveType->name,
            'approved_by' => $leaveRequest->approved_by,
            'start_date' => $leaveRequest->start_date->format('Y-m-d'),
            'end_date' => $leaveRequest->end_date->format('Y-m-d'),
            'statuses' => [],
        ];

        foreach ($statusHistory as $status) {
            $timelineItem['statuses'][] = [
                'name' => $status->name,
                'created_at' => $status->created_at->format('Y-m-d H:i:s'),
                'reason' => $status->reason,
            ];
        }

        $timelineData = [(object) $timelineItem];

        return view('leave.show', compact('page', 'description', 'timelineData'));
    }
    public function clockInOut(Request $request)
    {
        $page = 'Clock In';
        $description = '';
        return view('attendances.employee-clockin', compact('page', 'description'));
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

    public function accountSettings()
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->firstOrFail();

        return view('employee.settings', compact('employee'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->firstOrFail();

        // Validate the incoming data
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'gender' => 'required|in:male,female',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'alternate_phone' => 'nullable|string|max:20',
            'date_of_birth' => 'required|date',
            'place_of_birth' => 'nullable|string|max:255',
            'marital_status' => 'required|in:single,married,divorced,widowed',
            'address' => 'required|string|max:255',
            'permanent_address' => 'nullable|string|max:255',
            'blood_group' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'avatar' => 'nullable|image|max:2048', // Max 2MB for profile picture
            'spouse_surname_name' => 'nullable|string|max:255',
            'spouse_first_name' => 'nullable|string|max:255',
            'spouse_middle_name' => 'nullable|string|max:255',
            'spouse_date_of_birth' => 'nullable|date',
            'spouse_phone' => 'nullable|string|max:20',
            'emmergency_contact_name.*' => 'required|string|max:255',
            'emmergency_contact_relationship.*' => 'required|string|max:255',
            'emmergency_contact_phone.*' => 'required|string|max:20',
        ]);

        // Handle file upload for avatar
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $avatarPath;
        }

        // Update employee details
        $employee->update($validated);

        // Handle emergency contacts (assuming a separate table or JSON column)
        if ($request->has('emmergency_contact_name')) {
            $emergencyContacts = [];
            foreach ($request->emmergency_contact_name as $index => $name) {
                $emergencyContacts[] = [
                    'name' => $name,
                    'relationship' => $request->emmergency_contact_relationship[$index],
                    'phone' => $request->emmergency_contact_phone[$index],
                ];
            }
            // Save to a JSON column or related table
            $employee->emergency_contacts = json_encode($emergencyContacts); // If using JSON column
            $employee->save();
        }

        return redirect()->route('account.settings')->with('success', 'Profile updated successfully!');
    }
}
