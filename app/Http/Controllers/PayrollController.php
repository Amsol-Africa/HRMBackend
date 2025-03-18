<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\Business;
use App\Models\Employee;
use App\Models\Location;
use App\Mail\PayslipMail;
use Illuminate\Http\Request;
use App\Http\RequestResponse;
use App\Models\EmployeePayroll;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\PayrollService;
use App\Services\PayslipService;
use App\Traits\HandleTransactions;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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
        Log::debug($payslip);
        Log::debug($payslip->deductions);

        // Decode the deductions JSON string into a PHP array
        $payslip->deductions = json_decode($payslip->deductions, true);

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
            'locations' => 'nullable|array',
            'locations.*' => 'exists:locations,slug',
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

            $errors = "";

            foreach ($employees as $employee) {
                try {

                    $businessSlug = $business->slug;
                    $directoryPath = storage_path('app/payslips/' . $businessSlug);
                    $pdfPath = $directoryPath . '/' . $employee->employee_code . '_' . $validatedData['payrun_year'] . '_' . $validatedData['payrun_month'] . '.pdf';

                    Log::debug("PDF Save Path: " . $pdfPath);
                    Log::debug("Calculating payroll for employee ID: {$employee->id}");

                    // Create directory if it doesn't exist
                    if (!is_dir($directoryPath)) {
                        mkdir($directoryPath, 0755, true);
                        Log::debug("Created directory: " . $directoryPath);
                    }

                    $payslipData = $this->payrollService->calculatePayroll(
                        $employee,
                        $validatedData['payrun_year'],
                        $validatedData['payrun_month'],
                        $payroll->id
                    );

                    $payslipData->deductions = json_decode($payslipData->deductions, true);

                    $payslipData['payrun_year'] = $validatedData['payrun_year'];
                    $payslipData['payrun_month'] = $validatedData['payrun_month'];

                    Log::debug($payslipData);
                    $pdf = Pdf::loadView('pdf.payslip', ['payslip' => $payslipData]);
                    $pdf->save($pdfPath);

                    Mail::to($employee->user->email)->send(new PayslipMail($employee, [
                        'month' => $validatedData['payrun_month'],
                        'year' => $validatedData['payrun_year'],
                        'business' => $business,
                    ], $pdfPath));

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

    public function downloadSlip(Request $request)
    {
        $payslip = EmployeePayroll::findOrFail($request->payslip);
        $employee = $payslip->employee;
        $payrun_year = $payslip->payroll->payrun_year;
        $payrun_month = $payslip->payroll->payrun_month;

        $business = Business::findBySlug(session('active_business_slug'));
        $businessSlug = $business->slug;
        $directoryPath = storage_path('app/payslips/' . $businessSlug);
        $pdfPath = $directoryPath . '/' . $employee->employee_code . '_' . $payrun_year . '_' . $payrun_month . '.pdf';

        if (!file_exists($pdfPath)){
            if (!is_dir($directoryPath)) {
                mkdir($directoryPath, 0755, true);
            }

            $pdf = Pdf::loadView('pdf.payslip', ['payslip' => $payslip]);
            $pdf->save($pdfPath);
        }

        return RequestResponse::download($pdfPath, $employee->employee_code . '_' . $payrun_year . '_' . $payrun_month . '.pdf');
    }

    public function emailSlip(Request $request)
    {
        $payslip = EmployeePayroll::findOrFail($request->payslip);
        $employee = $payslip->employee;
        $payrun_year = $payslip->payroll->payrun_year;
        $payrun_month = $payslip->payroll->payrun_month;

        $business = Business::findBySlug(session('active_business_slug'));
        $businessSlug = $business->slug;
        $directoryPath = storage_path('app/payslips/' . $businessSlug);
        $pdfPath = $directoryPath . '/' . $employee->employee_code . '_' . $payrun_year . '_' . $payrun_month . '.pdf';

        if (!file_exists($pdfPath)){
            if (!is_dir($directoryPath)) {
                mkdir($directoryPath, 0755, true);
            }

            $pdf = Pdf::loadView('pdf.payslip', ['payslip' => $payslip]);
            $pdf->save($pdfPath);
        }

        try {
            Mail::to($employee->user->email)->send(new PayslipMail($employee, [
                'month' => $payrun_month,
                'year' => $payrun_year, 
                'business' => $business,
            ], $pdfPath));

            return RequestResponse::ok('Payslip emailed successfully.');
        } catch (\Exception $e) {
            Log::error("Failed to email payslip for employee ID {$employee->id}: " . $e->getMessage());
            return RequestResponse::internalServerError('Failed to email payslip.');
        }
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
