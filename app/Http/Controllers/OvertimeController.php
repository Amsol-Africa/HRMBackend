<?php

namespace App\Http\Controllers;

use App\Enum\Status;
use App\Models\Business;
use App\Models\Overtime;
use Illuminate\Http\Request;
use App\Http\RequestResponse;
use App\Traits\HandleTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OvertimeController extends Controller
{
    use HandleTransactions;

    public function fetch(Request $request)
    {
        $business = Business::findBySlug(session('active_business_slug'));

        $startDate = now()->startOfMonth()->toDateString();
        $endDate = now()->endOfMonth()->toDateString();


        // $overtimes = Overtime::where('business_id', $business->id)
        //     ->whereBetween('date', [$startDate, $endDate])
        //     ->select('employee_id', DB::raw('SUM(overtime_hours) as total_overtime_hours'))
        //     ->groupBy('employee_id')
        //     ->with('employee.user')
        //     ->latest()->get();


        $overtimes = Overtime::where('business_id', $business->id)
            ->with('employee.user', 'approvedBy')
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->get();

        Log::debug($overtimes);

        $overtimeTable = view('attendances._overtime_table', compact('overtimes'))->render();
        return RequestResponse::ok('Ok.', $overtimeTable);
    }


    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'overtime_hours' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $user = auth()->user();
            $business = Business::findBySlug(session('active_business_slug'));

            $overtime = Overtime::create([
                'employee_id' => $validatedData['employee_id'],
                'business_id' => $business->id,
                'date' => $validatedData['date'],
                'overtime_hours' => $validatedData['overtime_hours'],
                'rate' => $this->getOvertimeRate($business),
                'total_pay' => $validatedData['overtime_hours'] * $this->getOvertimeRate($business),
                'description' => $validatedData['description'],
                'approved_by' => $user->id,
            ]);

            $overtime->setStatus(Status::APPROVED);


            return RequestResponse::created('Overtime added and approved suceesfully.');
        });
    }

    private function getOvertimeRate($business)
    {
        return $business->overtime_rate ?? 1.5;
    }
}
