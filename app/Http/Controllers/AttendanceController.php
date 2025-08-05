<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Business;
use App\Models\Overtime;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Http\RequestResponse;
use App\Traits\HandleTransactions;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{
    use HandleTransactions;

    public function fetch(Request $request)
    {
        $business = Business::findBySlug(session('active_business_slug'));

        $dateInput = $request->input('date');

        if ($dateInput) {
            try {
                $date = Carbon::parse($dateInput, 'Africa/Nairobi')->format('Y-m-d');
            } catch (\Exception $e) {
                Log::error("Invalid date format: " . $dateInput . " - Error: " . $e->getMessage());
                return RequestResponse::badRequest('Invalid date format provided.');
            }
        } else {
            $date = now('Africa/Nairobi')->format('Y-m-d');
        }

        $attendances = Attendance::where('business_id', $business->id)
            ->whereDate('date', $date)
            ->with('employee')
            ->orderBy('date', 'desc')
            ->get();

        $attendanceTable = view('attendances._attendance_table', compact('attendances'))->render();
        return RequestResponse::ok('Attendance records fetched successfully.', $attendanceTable);
    }

    public function monthly(Request $request)
    {
        $business = Business::findBySlug(session('active_business_slug'));

        $year = now('Africa/Nairobi')->year;
        $month = $request->input('month')
            ? Carbon::createFromDate($year, intval($request->input('month')), 1, 'Africa/Nairobi')
            : now('Africa/Nairobi');

        $startDate = $month->copy()->startOfMonth();
        $endDate = $month->copy()->endOfMonth();
        $daysInMonth = $endDate->day;

        $startDate = $startDate->toDateString();
        $endDate = $endDate->toDateString();

        $attendances = Attendance::where('business_id', $business->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->with('employee')
            ->get();

        $attendanceData = [];
        foreach ($attendances as $attendance) {
            $employeeId = $attendance->employee_id;
            $day = $attendance->date->day;
            $attendanceData[$employeeId][$day] = $attendance;
        }

        $attendanceTable = view('attendances._monthly_attendance_table', [
            'attendanceData' => $attendanceData,
            'daysInMonth' => $daysInMonth,
            'month' => $month->format('F Y'),
        ])->render();

        return RequestResponse::ok('Ok.', $attendanceTable);
    }

    public function clockIn(Request $request)
    {
        return $this->handleTransaction(function () use ($request) {
            $user = auth()->user();
            $business = Business::findBySlug(session('active_business_slug'));
            $today = now('Africa/Nairobi')->toDateString();

            // Determine employee_id based on active role
            $employee_id = null;
            $active_role = session('active_role');

            if ($active_role === 'business-employee') {
                $employee = $user->employee;
                if (!$employee) {
                    return RequestResponse::badRequest('No employee record found for this user.');
                }
                $employee_id = $employee->id;
                // Ignore any employee_id from the request for employees
                if ($request->has('employee_id') && $request->input('employee_id') != $employee_id) {
                    return RequestResponse::badRequest('Unauthorized: Employees can only clock in for themselves.');
                }
            } else {
                // For admins (business-admin, business-hr, business-finance)
                if (!in_array($active_role, ['business-admin', 'business-hr', 'business-finance'])) {
                    return RequestResponse::badRequest('Unauthorized: Only admins or employees can clock in.');
                }

                $validatedData = $request->validate([
                    'employee_id' => 'required|exists:employees,id',
                    'is_absent' => 'sometimes|boolean',
                    'remarks' => 'nullable|string|max:255',
                ], [
                    'employee_id.required' => 'Please select an employee.',
                    'employee_id.exists' => 'The selected employee does not exist.',
                ]);

                $employee_id = $validatedData['employee_id'];
            }

            // Check for existing attendance
            $existingAttendance = Attendance::where([
                'employee_id' => $employee_id,
                'business_id' => $business->id,
                'date' => $today,
            ])->first();

            if ($existingAttendance) {
                if (!is_null($existingAttendance->clock_out)) {
                    return RequestResponse::badRequest('You have already completed today’s attendance.');
                }
                return RequestResponse::badRequest('You are already clocked in.');
            }

            Attendance::create([
                'employee_id' => $employee_id,
                'business_id' => $business->id,
                'date' => $today,
                'clock_in' => now('Africa/Nairobi')->format('H:i:s'),
                'is_absent' => $request->input('is_absent', false),
                'remarks' => $request->input('remarks'),
                'logged_by' => $user->id,
            ]);

            return RequestResponse::created('Clock-in successful.');
        });
    }

    public function clockIns(Request $request)
    {
        $business = Business::findBySlug(session('active_business_slug'));
        $date = now('Africa/Nairobi')->format('Y-m-d');

        $query = Attendance::where('business_id', $business->id)
            ->whereDate('date', $date)
            ->with(['employee', 'employee.user']);

        $clockins = $query->orderBy('created_at', 'desc')->get();
        $clockinsCards = view('attendances._clock_ins', compact('clockins'))->render();

        return RequestResponse::ok('Ok.', $clockinsCards ?: '<p>No clock-ins found for today.</p>');
    }

    public function clockOut(Request $request)
    {
        return $this->handleTransaction(function () use ($request) {
            $user = auth()->user();
            $business = Business::findBySlug(session('active_business_slug'));
            $today = now('Africa/Nairobi')->toDateString();

            // Determine employee_id based on user role
            $employee_id = null;
            if ($user->hasRole('business-employee')) {
                $employee = $user->employee;
                if (!$employee) {
                    return RequestResponse::badRequest('No employee record found for this user.');
                }
                $employee_id = $employee->id;
                if ($request->has('employee') && $request->input('employee') != $employee_id) {
                    return RequestResponse::badRequest('Unauthorized: Employees can only clock out themselves.');
                }
            } else {
                $validatedData = $request->validate([
                    'employee' => 'required|exists:employees,id',
                    'remarks' => 'nullable|string|max:255',
                ]);
                $employee_id = $validatedData['employee'];
            }

            $attendance = Attendance::where([
                'employee_id' => $employee_id,
                'business_id' => $business->id,
                'date' => $today,
            ])->first();

            if (!$attendance || !$attendance->clock_in) {
                return RequestResponse::badRequest('You need to clock in first.');
            }

            if ($attendance->clock_out) {
                return RequestResponse::badRequest('You have already clocked out today.');
            }

            $attendance->update([
                'clock_out' => now('Africa/Nairobi')->format('H:i:s'),
                'overtime_hours' => max(0, Carbon::parse($attendance->clock_in, 'Africa/Nairobi')->diffInHours(now('Africa/Nairobi')) - 8),
                'remarks' => $request->input('remarks', $attendance->remarks),
            ]);

            return RequestResponse::created('Clock-out recorded successfully.');
        });
    }

    private function getOvertimeRate($business)
    {
        return $business->overtime_rate ?? 1.5;
    }
}