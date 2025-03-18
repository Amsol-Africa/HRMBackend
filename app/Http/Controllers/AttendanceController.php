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
                $date = Carbon::parse($dateInput)->format('Y-m-d');
            } catch (\Exception $e) {
                Log::error("Invalid date format: " . $dateInput . " - Error: " . $e->getMessage());
                return RequestResponse::badRequest('Invalid date format provided.');
            }
        } else {
            $date = now()->format('Y-m-d');
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

        $year = now()->year;
        $month = $request->input('month')
            ? Carbon::createFromDate($year, intval($request->input('month')), 1)
            : now();


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
        $validatedData = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'clock_in' => 'nullable|date_format:H:i',
            'is_absent' => 'sometimes|boolean',
            'remarks' => 'nullable|string',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $user = auth()->user();
            $business = Business::findBySlug(session('active_business_slug'));
            $today = now()->toDateString();

            $existingAttendance = Attendance::where([
                'employee_id' => $validatedData['employee_id'],
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
                'employee_id' => $validatedData['employee_id'],
                'business_id' => $business->id,
                'date' => $today,
                'clock_in' => now()->format("H:i"),
                'is_absent' => $validatedData['is_absent'] ?? false,
                'remarks' => $validatedData['remarks'] ?? null,
                'logged_by' => $user->id,
            ]);

            return RequestResponse::created('Clock-in successful.');
        });
    }

    public function clockIns(Request $request)
    {
        $business = Business::findBySlug(session('active_business_slug'));
        $date = now()->format('Y-m-d');

        $query = Attendance::where('business_id', $business->id)
            ->whereDate('date', $date)
            ->with('employee');

        if (auth()->user()->hasRole('employee')) {
            $query->where('employee_id', auth()->user()->employee->id);
        }

        $clockins = $query->orderBy('date', 'desc')->get();
        $clockinsCards = view('attendances._clock_ins', compact('clockins'))->render();

        return RequestResponse::ok('Ok.', $clockinsCards);
    }

    public function clockOut(Request $request)
    {
        $validatedData = $request->validate([
            'employee' => 'required|exists:employees,id',
            'remarks' => 'nullable|string',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $business = Business::findBySlug(session('active_business_slug'));
            $attendance = Attendance::where([
                'employee_id' => $validatedData['employee'],
                'business_id' => $business->id,
                'date' => now()->format('Y-m-d'),
            ])->first();

            if (!$attendance || !$attendance->clock_in) {
                return RequestResponse::badRequest('You need to clock in first.');
            }

            if ($attendance->clock_out) {
                return RequestResponse::badRequest('You have already clocked out today.');
            }

            $attendance->update([
                'clock_out' => now()->format('H:i'),
                'overtime_hours' => max(0, Carbon::parse($attendance->clock_in)->diffInHours(now()) - 8),
            ]);

            return RequestResponse::created('Clock-out recorded successfully.');
        });
    }

    private function getOvertimeRate($business)
    {
        return $business->overtime_rate ?? 1.5;
    }
}