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
            'date' => 'required|date',
            'clock_in' => 'nullable|date_format:H:i',
            'is_absent' => 'sometimes|boolean',
            'remarks' => 'nullable|string',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $user = auth()->user();
            $business = Business::findBySlug(session('active_business_slug'));

            $validatedData['date'] = now();

            if (!empty($validatedData['is_absent'])) {
                $validatedData['clock_in'] = null;
            }

            $attendance = Attendance::create([
                'employee_id' => $validatedData['employee_id'],
                'business_id' => $business->id,
                'date' => $validatedData['date'],
                'clock_in' => now()->format("H:i"),
                'is_absent' => $validatedData['is_absent'] ?? false,
                'remarks' => $validatedData['remarks'],
                'logged_by' => $user->id,
            ]);

            return RequestResponse::created('Attendance logged successfully.');
        });
    }

    public function clockIns(Request $request)
    {
        $business = Business::findBySlug(session('active_business_slug'));
        $date = now()->format('Y-m-d');

        $clockins = Attendance::where('business_id', $business->id)
            ->whereDate('date', $date)
            ->with('employee')
            ->orderBy('date', 'desc')
            ->get();

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

            $attendance = Attendance::where('employee_id', $validatedData['employee'])
                ->where('business_id', $business->id)
                ->whereDate('date', now()->format('Y-m-d'))
                ->first();

            if (!$attendance || !$attendance->clock_in) {
                return RequestResponse::badRequest('Clock-in record not found.');
            }

            $clockIn = Carbon::parse($attendance->clock_in);
            $clockOut = now()->format('H:i');
            $workHours = $clockIn->diffInHours($clockOut);

            $overtimeHours = max(0, $workHours - 8);

            $attendance->update([
                'clock_out' => $clockOut,
                'overtime_hours' => $overtimeHours,
            ]);

            if ($overtimeHours > 0) {
                Overtime::create([
                    'employee_id' => $validatedData['employee'],
                    'business_id' => $business->id,
                    'date' => now()->format('Y-m-d'),
                    'overtime_hours' => $overtimeHours,
                    'rate' => $this->getOvertimeRate($business),
                    'total_pay' => $overtimeHours * $this->getOvertimeRate($business),
                ]);
            }

            return RequestResponse::created('Clock-out recorded successfully.');
        });
    }

    private function getOvertimeRate($business)
    {
        return $business->overtime_rate ?? 1.5;
    }

}
