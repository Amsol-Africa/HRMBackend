<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\Business;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Http\RequestResponse;
use App\Services\PayrollService;
use App\Traits\HandleTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PayrollController extends Controller
{
    protected $payrollService;
    use HandleTransactions;

    public function __construct(PayrollService $payrollService)
    {
        $this->payrollService = $payrollService;
    }

    public function fetch()
    {
        $business = Business::findBySlug(session('active_business_slug'));
        $payrolls = Payroll::getPayrolls($business->id);
        $payrollTable = view('payroll._payroll_list', compact('payrolls'))->render();
        return RequestResponse::ok('Payrolls retrieved successfully.', $payrollTable);
    }

    public function slips(Request $request)
    {
        $payroll = Payroll::findOrFail($request->payroll);
        $payslips = $payroll->getPayslips();
        $payslipTable = view('payroll._payslips_table', compact('payslips'))->render();
        return RequestResponse::ok('Payslips retrieved successfully.', $payslipTable);
    }

    public function showSlip(Request $request)
    {
        $payroll = Payroll::findOrFail($request->payroll);
        $payslips = $payroll->getPayslips();
        $payslipData = view('payroll._payslip_details', compact('payslips'))->render();
        return RequestResponse::ok('Payslip retrieved successfully.', $payslipData);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'employees' => 'required|array',
            'employees.*' => 'exists:employees,id',
            'location' => 'nullable|exists:locations,slug', // Optional location
        ]);

        return $this->handleTransaction(function () use ($validatedData) {

            $business = Business::findBySlug(session('active_business_slug'));

            $payroll = Payroll::create([
                'business_id' => $business->id,
                'location_id' => null,
                'payroll_type' => 'monthly',
                'currency' => 'KSH',
                'staff' => count($validatedData['employees']),
                'start_date' => $validatedData['start_date'],
                'end_date' => $validatedData['end_date'],
            ]);

            $employeeIds = $validatedData['employees'];
            $employees = Employee::whereIn('id', $employeeIds)->get();

            $payrollResults = [];
            $errors = "";

            foreach ($employees as $employee) {
                try {
                    Log::debug("Calculating payroll for employee ID: {$employee->id}");

                    $payrollData = $this->payrollService->calculatePayroll($employee, $validatedData['start_date'], $validatedData['end_date'], $payroll->id, );

                    $payrollResults[] = [
                        'employee_id' => $employee->id,
                        'name' => $employee->name,
                        'net_pay' => $payrollData['net_pay'],
                    ];

                } catch (\Exception $e) {
                    Log::error("Payroll calculation failed for employee ID {$employee->id}: " . $e->getMessage());
                    $errors .= "Payroll calculation failed for {$employee->name} (ID: {$employee->id}): " . $e->getMessage() . "\n";
                }
            }

            if (!empty($errors)) {
                return RequestResponse::badRequest('Payroll processing failed for some employees.', trim($errors));
            }

            return RequestResponse::created('Payroll processed successfully.', $payroll->id);
        });
    }
    public function downloadCsvTemplate()
    {
        $filePath = storage_path('app/public/templates/payroll_template.csv');
        $fileName = 'payroll_template.csv';

        return response()->download($filePath, $fileName, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function downloadXlsxTemplate()
    {
        $filePath = storage_path('app/public/templates/payroll_template.xlsx');
        $fileName = 'payroll_template.xlsx';

        return response()->download($filePath, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

}
