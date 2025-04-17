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
use App\Models\EmployeePayroll;
use App\Models\Location;
use App\Http\Responses\RequestResponse;
use Barryvdh\DomPDF\Facade\Pdf;

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

    public function updateDetails(Request $request)
    {
        $page = 'Update Your Details';
        $description = '';
        return view('employees.update-details', compact('page', 'description'));
    }

    // Download P9 Form
    public function downloadP9(Request $request)
    {
        $employee = Auth::user()->employee;
        if (!$employee) {
            return back()->with('error', 'Employee record not found.');
        }

        $year = $request->query('year', now()->year); // Default to current year

        $payrolls = EmployeePayroll::where('employee_id', $employee->id)
            ->whereHas('payroll', fn($q) => $q->where('payrun_year', $year))
            ->with('payroll')
            ->get();

        if ($payrolls->isEmpty()) {
            return back()->with('error', "No payroll data available for $year.");
        }

        $data = $this->prepareP9Data($employee, $payrolls, $year);
        $pdf = Pdf::loadView('payroll.reports.p9_employee', [
            'employee' => $employee,
            'year' => $year,
            'data' => $data,
        ])->setPaper('a4', 'landscape');

        return $pdf->download("P9_{$year}_{$employee->employee_code}.pdf");
    }

    public function viewPayslips(Request $request, $business)
    {
        $business = Business::findBySlug($business);
        if (!$business || session('active_business_slug') !== $business->slug) {
            return redirect()->back()->with('error', 'Business not found or mismatched.');
        }

        $employee = Employee::where('business_id', $business->id)
            ->where('user_id', Auth::id())
            ->first();
        if (!$employee) {
            return redirect()->back()->with('error', 'Employee record not found.');
        }

        $payslips = EmployeePayroll::where('employee_id', $employee->id)
            ->with(['payroll'])
            ->get()
            ->map(function ($ep) {
                return [
                    'payroll_id' => $ep->payroll_id,
                    'year' => $ep->payroll->payrun_year,
                    'month' => $ep->payroll->payrun_month,
                    'month_name' => Carbon::create($ep->payroll->payrun_year, $ep->payroll->payrun_month, 1)->monthName,
                    'status' => $ep->payroll->status,
                ];
            })
            ->sortByDesc('year')
            ->sortByDesc('month')
            ->values();

        log::info($payslips);

        $page = "My Payslips";
        return view('employee.payslips', compact('page', 'payslips', 'employee', 'business'));
    }

    public function downloadPayslip($payrollId)
    {
        $employee = Auth::user()->employee;
        if (!$employee) {
            return back()->with('error', 'Employee record not found.');
        }

        $employeePayroll = EmployeePayroll::where('employee_id', $employee->id)
            ->where('payroll_id', $payrollId)
            ->with(['payroll.business', 'employee.user'])
            ->first();

        if (!$employeePayroll) {
            return back()->with('error', 'Payslip not found!');
        }

        if ($employeePayroll->payroll->status !== 'closed') {
            return back()->with('error', 'Payslip is not available until payroll is closed.');
        }

        $pdf = Pdf::loadView('payroll.reports.payslip', [
            'employeePayroll' => $employeePayroll,
            'business' => $employeePayroll->payroll->business,
            'entity' => $employeePayroll->payroll->business,
            'entityType' => 'business',
            'exchangeRates' => ['rate' => 1],
            'targetCurrency' => $employeePayroll->payroll->currency ?? 'KES',
        ]);

        $monthName = Carbon::create($employeePayroll->payroll->payrun_year, $employeePayroll->payroll->payrun_month, 1)->monthName;
        return $pdf->download("Payslip_{$employeePayroll->payroll->payrun_year}_{$monthName}_{$employee->employee_code}.pdf");
    }

    private function prepareP9Data($employee, $payrolls, $year)
    {
        $monthlyData = array_fill(1, 12, [
            'basic_salary' => 0,              // A
            'benefits_non_cash' => 0,         // B
            'value_of_quarters' => 0,         // C
            'total_gross_pay' => 0,           // D
            'retirement_e1' => 0,             // E1 (30% of A)
            'retirement_e2' => 0,             // E2 (Actual)
            'retirement_e3' => 20000,         // E3 (Fixed KRA limit)
            'owner_occupied_interest' => 0,   // F
            'retirement_contribution' => 0,   // G (Lowest of E + F)
            'chargeable_pay' => 0,            // H
            'tax_charged' => 0,               // J
            'personal_relief' => 2400,        // K (KRA standard monthly max)
            'insurance_relief' => 0,          // K
            'paye' => 0,                      // J-K
        ]);

        foreach ($payrolls as $ep) {
            $month = (int)$ep->payroll->payrun_month;
            $deductions = json_decode($ep->deductions, true) ?? [];
            $basicSalary = (float)$ep->basic_salary;
            $grossPay = (float)$ep->gross_pay;
            $taxableIncome = (float)$ep->taxable_income;
            $paye = (float)$ep->paye;
            $personalRelief = (float)$ep->personal_relief ?? 2400;
            $insuranceRelief = (float)$ep->insurance_relief ?? 0;
            $retirementE1 = $basicSalary * 0.3; // 30% of basic salary
            $retirementE2 = (float)($ep->pension ?? ($deductions['pension'] ?? 0)); // Actual contribution

            $monthlyData[$month] = [
                'basic_salary' => $basicSalary,
                'benefits_non_cash' => 0, // Add logic if tracked
                'value_of_quarters' => 0, // Add logic if tracked
                'total_gross_pay' => $grossPay,
                'retirement_e1' => $retirementE1,
                'retirement_e2' => $retirementE2,
                'retirement_e3' => 20000,
                'owner_occupied_interest' => 0, // Add if applicable
                'retirement_contribution' => min($retirementE1, $retirementE2, 20000),
                'chargeable_pay' => $taxableIncome,
                'tax_charged' => $paye + $personalRelief + $insuranceRelief, // Reverse calculate J
                'personal_relief' => $personalRelief,
                'insurance_relief' => $insuranceRelief,
                'paye' => $paye,
            ];
        }

        $totals = [
            'basic_salary' => array_sum(array_column($monthlyData, 'basic_salary')),
            'benefits_non_cash' => array_sum(array_column($monthlyData, 'benefits_non_cash')),
            'value_of_quarters' => array_sum(array_column($monthlyData, 'value_of_quarters')),
            'total_gross_pay' => array_sum(array_column($monthlyData, 'total_gross_pay')),
            'retirement_e1' => array_sum(array_column($monthlyData, 'retirement_e1')),
            'retirement_e2' => array_sum(array_column($monthlyData, 'retirement_e2')),
            'retirement_e3' => array_sum(array_column($monthlyData, 'retirement_e3')),
            'owner_occupied_interest' => array_sum(array_column($monthlyData, 'owner_occupied_interest')),
            'retirement_contribution' => array_sum(array_column($monthlyData, 'retirement_contribution')),
            'chargeable_pay' => array_sum(array_column($monthlyData, 'chargeable_pay')),
            'tax_charged' => array_sum(array_column($monthlyData, 'tax_charged')),
            'personal_relief' => array_sum(array_column($monthlyData, 'personal_relief')),
            'insurance_relief' => array_sum(array_column($monthlyData, 'insurance_relief')),
            'paye' => array_sum(array_column($monthlyData, 'paye')),
        ];

        return [
            'employee_name' => $employee->full_name,
            'tax_no' => $employee->tax_no ?? 'N/A',
            'monthly_data' => $monthlyData,
            'totals' => $totals,
        ];
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