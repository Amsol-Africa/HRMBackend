<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Business;
use App\Models\Overtime;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Http\RequestResponse;
use App\Traits\HandleTransactions;

class AttendanceController extends Controller
{
    use HandleTransactions;

    // Fetch Job Posts
    public function fetch(Request $request)
    {
        $business = Business::findBySlug(session('active_business_slug'));

        $date = $request->input('date', now()->format('Y-m-d'));

        $attendances = Attendance::where('business_id', $business->id)
            ->whereDate('date', $date)
            ->with('employee')
            ->orderBy('date', 'desc')
            ->get();

        $attendanceTable = view('attendances._attendance_table', compact('attendances'))->render();

        return RequestResponse::ok('Attendance records fetched successfully.', $attendanceTable);
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

    public function clockOut(Request $request)
    {
        $validatedData = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'clock_out' => 'required|date_format:H:i',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $business = Business::findBySlug(session('active_business_slug'));

            $attendance = Attendance::where('employee_id', $validatedData['employee_id'])
                ->where('business_id', $business->id)
                ->whereDate('date', now()->format('Y-m-d'))
                ->first();

            if (!$attendance || !$attendance->clock_in) {
                return RequestResponse::badRequest('Clock-in record not found.');
            }

            $clockIn = Carbon::parse($attendance->clock_in);
            $clockOut = Carbon::parse($validatedData['clock_out']);
            $workHours = $clockIn->diffInHours($clockOut);

            $overtimeHours = max(0, $workHours - 8);

            $attendance->update([
                'clock_out' => $validatedData['clock_out'],
                'overtime_hours' => $overtimeHours,
            ]);

            if ($overtimeHours > 0) {
                Overtime::create([
                    'employee_id' => $validatedData['employee_id'],
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
