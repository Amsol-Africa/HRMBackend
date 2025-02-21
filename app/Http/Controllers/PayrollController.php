<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\Business;
use App\Models\Employee;
use App\Models\Location;
use Illuminate\Http\Request;
use App\Http\RequestResponse;
use App\Models\EmployeePayroll;
use App\Services\PayrollService;
use App\Services\PayslipService;
use App\Traits\HandleTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PayrollController extends Controller
{
    protected $payrollService, $payslipService;
    use HandleTransactions;

    public function __construct(PayrollService $payrollService, PayslipService $payslipService)
    {
        $this->payrollService = $payrollService;
        $this->payslipService = $payslipService;
    }

    public function fetch(Request $request)
    {
        $business = Business::findBySlug(session('active_business_slug'));

        $payrolls = Payroll::where('business_id', $business->id)
            ->orderByDesc('payrun_year')
            ->orderByDesc('payrun_month')
            ->get();

        $disp_location = $business->company_name;

        if ($request->has('location') && !empty($request->location)) {
            $location = Location::findBySlug($request->location);
            $payrolls = Payroll::where('location_id', $location->id)
                ->orderByDesc('payrun_year')
                ->orderByDesc('payrun_month')
                ->get();

            $disp_location = $location->name;
        }

        $payrollTable = view('payroll._payroll_list', compact('payrolls', 'disp_location'))->render();

        return RequestResponse::ok('Payrolls retrieved successfully.', $payrollTable);
    }

    public function slips(Request $request)
    {
        $payroll = Payroll::findOrFail($request->payroll);
        $payslips = $payroll->getPayslips();

        $totalEmployees = $payslips->count();
        $totalPayrollCost = $payslips->sum('gross_pay');
        $totalNetPay = $payslips->sum('net_pay');
        $totalTaxes = $payslips->sum('paye');
        $totalPreTaxDeductions = $payslips->sum('nhif') + $payslips->sum('nssf') + $payslips->sum('housing_levy');
        $totalPostTaxDeductions = $payslips->sum('deductions_after_tax');

        $period = date('F Y', strtotime($payroll->end_date ?? now()));

        $payDay = date('jS F Y', strtotime($payroll->end_date ?? now()->endOfMonth()));

        $payslipTable = view('payroll._payslips_table', compact('payslips'))->render();

        return RequestResponse::ok('Payslips retrieved successfully.', [
            'payslipTable' => $payslipTable,
            'summary' => [
                'period' => $period,
                'pay_day' => $payDay,
                'employees' => $totalEmployees,
                'payroll_cost' => $totalPayrollCost,
                'net_pay' => $totalNetPay,
                'taxes' => $totalTaxes,
                'pre_tax_deductions' => $totalPreTaxDeductions,
                'post_tax_deductions' => $totalPostTaxDeductions,
            ]
        ]);
    }

    public function showSlip(Request $request)
    {
        $payslip = EmployeePayroll::findOrFail($request->payslip);
        $payslipData = view('payroll._payslip_details', compact('payslip'))->render();
        return RequestResponse::ok('Payslip retrieved successfully.', $payslipData);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'payrun_year' => 'required|integer|min:' . (now()->year - 5) . '|max:' . (now()->year + 1),
            'payrun_month' => [
                'required',
                'integer',
                'min:1',
                'max:12',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->payrun_year == now()->year && $value > now()->month) {
                        $fail("You cannot process payroll for future months.");
                    }
                },
            ],
            'employees' => 'required|array',
            'employees.*' => 'exists:employees,id',
            'locations' => 'nullable|array',  // Adjusted to locations[] instead of location[]
            'locations.*' => 'exists:locations,slug', // Check each element in locations[]
            'repay_loans' => 'nullable|boolean',
            'recover_advance' => 'nullable|boolean',
            'pay_overtime' => 'nullable|boolean',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {

            $business = Business::findBySlug(session('active_business_slug'));

            $locationSlug = $validatedData['locations'][0] ?? null;

            $locationId = $locationSlug ? Location::where('slug', $locationSlug)->first()->id : null;

            $payroll = Payroll::create([
                'business_id' => $business->id,
                'location_id' => $locationId,
                'payroll_type' => 'monthly',
                'currency' => 'KSH',
                'staff' => count($validatedData['employees']),
                'payrun_year' => $validatedData['payrun_year'],
                'payrun_month' => $validatedData['payrun_month'],
            ]);

            $employeeIds = $validatedData['employees'];
            $employees = Employee::whereIn('id', $employeeIds)->get();

            $payrollResults = [];
            $errors = "";

            foreach ($employees as $employee) {
                try {
                    Log::debug("Calculating payroll for employee ID: {$employee->id}");

                    $payrollData = $this->payrollService->calculatePayroll(
                        $employee,
                        $validatedData['payrun_year'],
                        $validatedData['payrun_month'],
                        $payroll->id
                    );

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

            return RequestResponse::created('Payroll processed successfully.');
        });
    }


    public function printPayslip($id)
    {
        $payslip = EmployeePayroll::findOrFail($id);
        return $this->payslipService->generatePayslipPdf($payslip->id);
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
