<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Location;
use App\Models\Department;
use App\Models\JobCategory;
use App\Models\Payroll;
use App\Models\Employee;
use App\Models\EmployeePayroll;
use App\Models\Overtime;
use App\Models\Advance;
use App\Models\Loan;
use App\Models\LoanRepayment;
use App\Models\Deduction;
use App\Models\Allowance;
use App\Models\Relief;
use App\Models\EmployeeDeduction;
use App\Models\EmployeeAllowance;
use App\Models\EmployeeRelief;
use App\Models\PayrollFormula;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Exports\PayrollExport;
use Illuminate\Http\Request;
use App\Http\RequestResponse;
use App\Traits\HandleTransactions;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\PayslipMail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;


class PayrollController extends Controller
{
    use HandleTransactions;

    public function index(Request $request)
    {
        $business = Business::findBySlug(session('active_business_slug'));
        if (!$business) return RequestResponse::badRequest('Business not found.');

        return view('payroll.index', [
            'page' => 'Process Payroll',
            'years' => range(date('Y'), date('Y') + 5),
            'months' => range(1, 12),
            'locations' => $business->locations ?? collect(),
            'departments' => $business->departments ?? collect(),
            'jobCategories' => $business->job_categories ?? collect(),
            'payrolls' => $business->payrolls ?? collect(),
            'allowances' => $business->allowances ?? collect(),
            'deductions' => $business->deductions ?? collect(),
            'reliefs' => $business->reliefs ?? collect(),
            'payrollFormulas' => $business->payrollFormulas ?? collect(),
            'employees' => Employee::where('business_id', $business->id)->with('user')->get(['id', 'user_id']),
        ]);
    }

    public function fetch(Request $request)
    {
        $business = Business::findBySlug(session('active_business_slug'));
        if (!$business) return RequestResponse::badRequest('Business not found.');

        $payroll = Payroll::where('business_id', $business->id)
            ->where('payrun_year', $request->year)
            ->where('payrun_month', $request->month)
            ->first();

        if ($payroll && $payroll->status === 'closed') {
            return RequestResponse::badRequest('This month has already been closed.');
        }

        $employees = $this->getFilteredEmployees($request, $business);
        $warnings = $this->checkMissingData($employees);
        $options = $this->parseOptions($request);

        return RequestResponse::ok('Employees fetched successfully.', [
            'html' => view('payroll._table', [
                'employees' => $employees,
                'warnings' => $warnings,
                'options' => $options,
                'allowances' => $business->allowances,
                'deductions' => $business->deductions,
                'reliefs' => $business->reliefs,
            ])->render(),
            'count' => $employees->count(),
            'warnings' => $warnings,
            'options' => $options,
        ]);
    }

    private function calculateAdvanceRecovery($employeeId, $year, $month, $options)
    {
        if ($options['recover_advances']['apply'] === 'all' || in_array($employeeId, $options['recover_advances']['employees'])) {
            $totalAdvances = Advance::where('employee_id', $employeeId)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->where('amount', '>', 0)
                ->sum('amount') ?? 0;
            return in_array($employeeId, $options['recover_advances']['employees']) && isset($options['recover_advances']['amounts'][$employeeId])
                ? min($totalAdvances, (float)$options['recover_advances']['amounts'][$employeeId])
                : $totalAdvances;
        }
        return 0;
    }

    private function calculateAbsenteeism($employee, $basicSalary, $options)
    {
        if ($options['charge_absenteeism']['apply'] === 'all' || in_array($employee->id, $options['charge_absenteeism']['employees'])) {
            $absentDays = $employee->attendances->where('is_absent', true)->count();
            $dailyRate = $basicSalary / 30;
            $charge = $absentDays * $dailyRate;
            return in_array($employee->id, $options['charge_absenteeism']['employees']) && isset($options['charge_absenteeism']['amounts'][$employee->id])
                ? min($charge, (float)$options['charge_absenteeism']['amounts'][$employee->id])
                : $charge;
        }
        return 0;
    }

    private function calculateHelb($employeeId, $grossPay)
    {
        // Placeholder; adjust based on actual HELB loan data
        return PayrollFormula::calculateForBusiness('HELB', $grossPay, session('active_business_slug')) ?? 0;
    }

    public function preview(Request $request)
    {
        $business = Business::findBySlug(session('active_business_slug'));
        $employees = $this->getFilteredEmployees($request, $business);
        $warnings = $this->checkMissingData($employees);

        if (!empty($warnings)) {
            return RequestResponse::badRequest('Please resolve all warnings before previewing.', ['warnings' => $warnings]);
        }

        $options = $this->parseOptions($request);
        $validEmployees = $employees->filter(fn($e) => !array_key_exists($e->id, $options['exempted_employees']) && $e->paymentDetails);
        $payrollData = $this->calculatePayroll($validEmployees, $request->year, $request->month, $options);

        return RequestResponse::ok('Payroll preview generated.', [
            'html' => view('payroll._preview', compact('payrollData', 'options'))->render(),
            'options' => $options,
        ]);
    }

    private function calculatePaye($taxableIncome)
    {
        // Kenyan PAYE brackets for 2025 (hypothetical, update from KRA)
        $brackets = [
            ['limit' => 24000, 'rate' => 0.10],
            ['limit' => 32333, 'rate' => 0.25],
            ['limit' => PHP_INT_MAX, 'rate' => 0.30], // Assuming 30% for income above KES 32,333
        ];

        $tax = 0;
        $remaining = $taxableIncome;
        foreach ($brackets as $bracket) {
            if ($remaining <= 0) break;
            $taxableInBracket = min($remaining, $bracket['limit'] - ($tax ? 24000 : 0));
            $tax += $taxableInBracket * $bracket['rate'];
            $remaining -= $taxableInBracket;
        }
        return $tax;
    }

    private function calculateReliefs($employee, $period)
    {
        return $employee->reliefs->mapWithKeys(function ($relief) use ($period) {
            $pivot = $relief->pivot;
            if ($pivot->is_active && (!$pivot->start_date || $pivot->start_date <= $period->endOfMonth()) && (!$pivot->end_date || $pivot->end_date >= $period->startOfMonth())) {
                return [$relief->id => [
                    'name' => $relief->name,
                    'amount' => $pivot->amount ?? $relief->fixed_amount ?? 0,
                    'tax_application' => $relief->tax_application,
                ]];
            }
            return [];
        })->filter()->toArray();
    }

    private function calculateOvertime($employeeId, $year, $month, $options)
    {
        if ($options['pay_overtime']['apply'] === 'all' || in_array($employeeId, $options['pay_overtime']['employees'])) {
            $totalOvertime = Overtime::where('employee_id', $employeeId)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->sum('total_pay') ?? 0;
            return in_array($employeeId, $options['pay_overtime']['employees']) && isset($options['pay_overtime']['amounts'][$employeeId])
                ? min($totalOvertime, (float)$options['pay_overtime']['amounts'][$employeeId])
                : $totalOvertime;
        }
        return 0;
    }

    public function store(Request $request)
    {
        $year = $request->year;
        $month = $request->month;
        $business = Business::findBySlug(session('active_business_slug'));

        $employees = $this->getFilteredEmployees($request, $business);

        DB::beginTransaction();
        try {
            $options = $this->parseOptions($request);
            $validEmployees = $employees->filter(fn($e) => !$e->is_exempt_from_payroll);
            $payrollData = $this->calculatePayroll($validEmployees, $year, $month, $options);

            $payroll = Payroll::create([
                'payrun_year' => $year,
                'payrun_month' => $month,
                'business_id' => $business->id,
                'location_id' => $request->location_id ?? null,
                'payroll_type' => 'monthly',
                'status' => 'open',
                'staff' => $validEmployees->count(),
                'currency' => $business->currency ?? 'KES',
            ]);

            foreach ($payrollData as $data) {
                EmployeePayroll::create([
                    'payroll_id' => $payroll->id,
                    'employee_id' => $data['employee_id'],
                    'basic_salary' => $data['basic_salary'],
                    'gross_pay' => $data['gross_pay'],
                    'taxable_income' => $data['taxable_income'],
                    'net_pay' => $data['net_pay'],
                    'overtime' => $data['overtime'],
                    'allowances' => json_encode($data['allowances']),
                    'deductions' => json_encode(array_merge($data['deductions'], [
                        'shif' => $data['shif'],
                        'nssf' => $data['nssf'],
                        'nhdf' => $data['housing_levy'],
                        'paye' => $data['paye'],
                        'helb' => $data['helb'],
                        'loan_repayment' => $data['loan_repayment'],
                        'advance_recovery' => $data['advance_recovery'],
                        'absenteeism_charge' => $data['absenteeism_charge'],
                    ])),
                    'reliefs' => json_encode($data['reliefs']),
                ]);
                $this->updateLoanAndAdvance($data, $year, $month, $options);
            }

            DB::commit();
            session(['active_business_slug' => $business->slug]);
            return RequestResponse::ok('Payroll processed successfully.', [
                'redirect_url' => route('business.payroll.view', ['business' => $business->slug, 'id' => $payroll->id])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payroll processing failed: ' . $e->getMessage());
            return RequestResponse::badRequest('Failed to process payroll.');
        }
    }

    private function parseOptions(Request $request)
    {
        $exempted = $request->input('exempted_employees');
        if (is_string($exempted)) {
            $exempted = json_decode($exempted, true) ?? [];
        } elseif (!is_array($exempted)) {
            $exempted = [];
        }

        return [
            'exempted_employees' => $exempted,
            'charge_absenteeism' => [
                'apply' => $request->input('charge_absenteeism.apply', 'none'),
                'employees' => $request->input('charge_absenteeism.employees', []),
                'amounts' => $request->input('charge_absenteeism.amounts', []),
            ],
            'recover_advances' => [
                'apply' => $request->input('recover_advances.apply', 'all'),
                'employees' => $request->input('recover_advances.employees', []),
                'amounts' => $request->input('recover_advances.amounts', []),
            ],
            'recover_loans' => [
                'apply' => $request->input('recover_loans.apply', 'all'),
                'employees' => $request->input('recover_loans.employees', []),
                'amounts' => $request->input('recover_loans.amounts', []),
            ],
            'pay_overtime' => [
                'apply' => $request->input('pay_overtime.apply', 'all'),
                'employees' => $request->input('pay_overtime.employees', []),
                'amounts' => $request->input('pay_overtime.amounts', []),
            ],
        ];
    }

    private function getFilteredEmployees(Request $request, Business $business)
    {
        $query = Employee::where('business_id', $business->id)
            ->with([
                'user' => fn($q) => $q->select('id', 'email', 'name'),
                'employmentDetails' => fn($q) => $q->select('employee_id', 'job_category_id', 'department_id', 'employment_term'),
                'paymentDetails' => fn($q) => $q->select('employee_id', 'basic_salary', 'currency', 'payment_mode', 'account_name', 'account_number', 'bank_name'),
                'employeeAllowances' => fn($q) => $q->with('allowance'),
                'employeeDeductions' => fn($q) => $q->with('deduction'),
                'reliefs' => fn($q) => $q->withPivot('amount'),
                'overtimes' => fn($q) => $q->whereYear('date', $request->year)->whereMonth('date', $request->month),
                'advances' => fn($q) => $q->whereYear('date', $request->year)->whereMonth('date', $request->month),
                'loans' => fn($q) => $q->with('repayments')->where('start_date', '<=', Carbon::create($request->year, $request->month)->endOfMonth())->where('end_date', '>=', Carbon::create($request->year, $request->month)->startOfMonth()),
                'attendances' => fn($q) => $q->whereYear('date', $request->year)->whereMonth('date', $request->month),
            ]);

        if ($request->location_id) $query->where('location_id', $request->location_id);
        if ($request->department_id) $query->whereHas('employmentDetails', fn($q) => $q->where('department_id', $request->department_id));
        if ($request->job_category_id) $query->whereHas('employmentDetails', fn($q) => $q->where('job_category_id', $request->job_category_id));
        if ($request->contract_type) $query->whereHas('employmentDetails', fn($q) => $q->where('employment_term', $request->contract_type));
        if ($request->employee_ids) $query->whereIn('id', $request->employee_ids);

        return $query->get();
    }

    private function calculatePayroll($employees, $year, $month, $options = [])
    {
        $business = Business::findBySlug(session('active_business_slug'));
        $payrollData = [];
        $period = Carbon::create($year, $month);

        foreach ($employees as $employee) {
            $employeeId = $employee->id;
            $basicSalary = $employee->paymentDetails->basic_salary ?? 0;

            // Gross Pay Components
            $allowances = $employee->employeeAllowances->mapWithKeys(fn($ea) => $ea->allowance ? [$ea->allowance->id => [
                'name' => $ea->allowance->name,
                'amount' => $ea->amount ?? 0,
                'is_taxable' => $ea->allowance->is_taxable,
            ]] : [])->filter()->toArray();


            $totalTaxableAllowances = array_sum(array_map(fn($a) => $a['is_taxable'] ? $a['amount'] : 0, $allowances));
            $totalNonTaxableAllowances = array_sum(array_map(fn($a) => !$a['is_taxable'] ? $a['amount'] : 0, $allowances));

            $overtime = $this->calculateOvertime($employeeId, $year, $month, $options);
            $grossPay = $basicSalary + $totalTaxableAllowances + $totalNonTaxableAllowances + $overtime;

            // Reliefs
            $reliefs = $this->calculateReliefs($employee, $period);
            $totalReliefsBeforeTax = array_sum(array_map(fn($r) => $r['tax_application'] === 'before_tax' ? $r['amount'] : 0, $reliefs));
            $totalReliefsAfterTax = array_sum(array_map(fn($r) => $r['tax_application'] === 'after_tax' ? $r['amount'] : 0, $reliefs));

            // Deductions and Taxes (Kenya 2025)
            $deductions = $employee->employeeDeductions->mapWithKeys(fn($ed) => $ed->deduction ? [$ed->deduction->id => $ed->amount ?? 0] : [])->filter()->toArray();
            $totalCustomDeductions = array_sum($deductions);

            $shif = $grossPay * 0.0275; // SHIF 2.75% as of 2024, assuming unchanged in 2025
            $nssf = min(2160, $grossPay * 0.06); // NSSF Tier I & II cap at KES 2,160 (employee share)
            $housingLevy = $grossPay * 0.015; // Housing Levy 1.5%
            $taxableIncome = max(0, $grossPay - $nssf - $housingLevy - $totalReliefsBeforeTax);

            $paye = $this->calculatePaye($taxableIncome); // Updated PAYE calculation
            $personalRelief = $reliefs['personal-relief']['amount'] ?? 2400; // Default KES 2,400
            $payeAfterRelief = max(0, $paye - $personalRelief);

            $helb = min(5000, $this->calculateHelb($employeeId, $grossPay)); // HELB capped at KES 5,000
            $absenteeismCharge = $this->calculateAbsenteeism($employee, $basicSalary, $options);
            $advanceRecovery = $this->calculateAdvanceRecovery($employeeId, $year, $month, $options);
            $loanRepayment = $this->calculateLoanRepayment($employeeId, $year, $month, $options);

            $deductionsTotal = $shif + $nssf + $housingLevy + $payeAfterRelief + $helb + $totalCustomDeductions + $advanceRecovery + $loanRepayment + $absenteeismCharge;
            $netPay = $grossPay + $totalReliefsAfterTax - $deductionsTotal;

            $payrollData[$employeeId] = [
                'employee_id' => $employeeId,
                'employee' => $employee,
                'basic_salary' => $basicSalary,
                'gross_pay' => $grossPay,
                'overtime' => $overtime,
                'paye' => $payeAfterRelief,
                'shif' => $shif,
                'nssf' => $nssf,
                'housing_levy' => $housingLevy,
                'helb' => $helb,
                'taxable_income' => $taxableIncome,
                'personal_relief' => $personalRelief,
                'loan_repayment' => $loanRepayment,
                'advance_recovery' => $advanceRecovery,
                'absenteeism_charge' => $absenteeismCharge,
                'deductions_after_tax' => $deductionsTotal,
                'deductions_before_tax' => $shif + $nssf + $housingLevy + $payeAfterRelief + $helb + $totalCustomDeductions,
                'net_pay' => $netPay,
                'deductions' => $deductions,
                'allowances' => $allowances,
                'reliefs' => $reliefs,
                'bank_name' => $employee->paymentDetails->bank_name ?? 'N/A',
                'account_number' => $employee->paymentDetails->account_number ?? 'N/A',
                'currency' => $employee->paymentDetails->currency ?? 'KES',
                'payment_mode' => $employee->paymentDetails->payment_mode ?? 'N/A',
                'attendance_present' => $employee->attendances->where('is_absent', false)->count(),
                'attendance_absent' => $employee->attendances->where('is_absent', true)->count(),
            ];
        }
        return $payrollData;
    }

    private function updateLoanAndAdvance($payrollData, $year, $month, $options)
    {
        $employeeId = $payrollData['employee_id'];

        if (($options['recover_advances']['apply'] === 'all' || in_array($employeeId, $options['recover_advances']['employees'])) && $payrollData['advance_recovery'] > 0) {
            $advances = Advance::where('employee_id', $employeeId)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->where('amount', '>', 0)
                ->orderBy('date', 'asc')
                ->get();
            $remainingRecovery = $payrollData['advance_recovery'];
            foreach ($advances as $advance) {
                if ($remainingRecovery <= 0) break;
                $recovery = min($advance->amount, $remainingRecovery);
                $advance->update(['amount' => max(0, $advance->amount - $recovery)]);
                $remainingRecovery -= $recovery;
            }
        }

        if (($options['recover_loans']['apply'] === 'all' || in_array($employeeId, $options['recover_loans']['employees'])) && $payrollData['loan_repayment'] > 0) {
            $period = Carbon::create($year, $month);
            $loans = Loan::where('employee_id', $employeeId)
                ->where('start_date', '<=', $period->endOfMonth())
                ->where('end_date', '>=', $period->startOfMonth())
                ->orderBy('start_date', 'asc')
                ->get();
            $remainingRepayment = $payrollData['loan_repayment'];
            foreach ($loans as $loan) {
                if ($remainingRepayment <= 0) break;
                $repaid = LoanRepayment::where('loan_id', $loan->id)->sum('amount');
                $remainingLoanBalance = max(0, $loan->amount - $repaid);
                if ($remainingLoanBalance > 0) {
                    $repayment = min($remainingLoanBalance, $remainingRepayment);
                    LoanRepayment::create([
                        'loan_id' => $loan->id,
                        'amount' => $repayment,
                        'date' => $period->endOfMonth(),
                    ]);
                    $remainingRepayment -= $repayment;
                }
            }
        }
    }

    private function calculateLoanRepayment($employeeId, $year, $month, $options)
    {
        if ($options['recover_loans']['apply'] === 'all' || in_array($employeeId, $options['recover_loans']['employees'])) {
            $period = Carbon::create($year, $month);
            $loans = Loan::where('employee_id', $employeeId)
                ->where('start_date', '<=', $period->endOfMonth())
                ->where('end_date', '>=', $period->startOfMonth())
                ->get();

            $totalRepayment = 0;
            foreach ($loans as $loan) {
                $monthly = $loan->amount / $loan->term_months;
                $repaid = LoanRepayment::where('loan_id', $loan->id)->sum('amount');
                $remaining = max(0, $loan->amount - $repaid);
                if ($remaining > 0) $totalRepayment += min($monthly, $remaining);
            }
            return in_array($employeeId, $options['recover_loans']['employees']) && isset($options['recover_loans']['amounts'][$employeeId])
                ? min($totalRepayment, (float)$options['recover_loans']['amounts'][$employeeId])
                : $totalRepayment;
        }
        return 0;
    }

    private function checkMissingData($employees)
    {
        $warnings = [];
        foreach ($employees as $employee) {
            if (!$employee->paymentDetails) $warnings[$employee->id][] = 'Missing payment details';
            if (!$employee->tax_no) $warnings[$employee->id][] = 'Missing KRA PIN';
            if (!$employee->user || !$employee->user->email) $warnings[$employee->id][] = 'Missing email';
        }
        return $warnings;
    }

    public function all(Request $request)
    {
        $page = 'All Payrolls';
        $business = Business::findBySlug(session('active_business_slug'));
        if (!$business) {
            return RequestResponse::badRequest('Business not found.');
        }

        $years = range(date('Y') - 5, date('Y') + 5);
        $months = range(1, 12);
        $locations = $business->locations ?? collect();
        $departments = $business->departments ?? collect();
        $jobCategories = $business->job_categories ?? collect();

        $payrolls = Payroll::where('business_id', $business->id)
            ->withCount(['employeePayrolls as no_of_payslips'])
            ->with(['employeePayrolls' => function ($query) {
                $query->select('payroll_id', 'net_pay');
            }, 'location' => function ($query) {
                $query->select('id', 'name');
            }])
            ->get();

        $totalPayroll = $payrolls->sum(function ($payroll) {
            return $payroll->employeePayrolls->sum('net_pay');
        });
        $totalNetPay = $totalPayroll;

        return view('payroll.all', compact('business', 'page', 'years', 'months', 'locations', 'departments', 'jobCategories', 'payrolls', 'totalPayroll', 'totalNetPay'));
    }

    public function deletePayroll(Request $request, $id)
    {
        return $this->handleTransaction(function () use ($id) {
            $business = Business::findBySlug(session('active_business_slug'));
            if (!$business) {
                return RequestResponse::badRequest('Business not found.');
            }

            $payroll = Payroll::where('business_id', $business->id)->where('id', $id)->firstOrFail();
            $payroll->delete();

            return RequestResponse::ok('Payroll deleted successfully.');
        });
    }

    public function publishPayroll(Request $request, $id)
    {
        return $this->handleTransaction(function () use ($id) {
            $business = Business::findBySlug(session('active_business_slug'));
            if (!$business) {
                return RequestResponse::badRequest('Business not found.');
            }

            $payroll = Payroll::where('business_id', $business->id)->where('id', $id)->firstOrFail();
            $payroll->update(['published' => true]);

            return RequestResponse::ok('Payroll published successfully.');
        });
    }

    public function unpublishPayroll(Request $request, $id)
    {
        return $this->handleTransaction(function () use ($id) {
            $business = Business::findBySlug(session('active_business_slug'));
            if (!$business) {
                return RequestResponse::badRequest('Business not found.');
            }

            $payroll = Payroll::where('business_id', $business->id)->where('id', $id)->firstOrFail();
            $payroll->update(['published' => false]);

            return RequestResponse::ok('Payroll unpublished successfully.');
        });
    }

    public function emailPayslips(Request $request, $id)
    {
        return $this->handleTransaction(function () use ($id) {
            $business = Business::findBySlug(session('active_business_slug'));
            if (!$business) {
                return RequestResponse::badRequest('Business not found.');
            }

            $payroll = Payroll::where('business_id', $business->id)
                ->where('id', $id)
                ->with(['employeePayrolls.employee.user'])
                ->firstOrFail();

            foreach ($payroll->employeePayrolls as $employeePayroll) {
                $employee = $employeePayroll->employee;
                $user = $employee->user;

                if ($user && $user->email) {
                    $pdf = Pdf::loadView('payroll.payslip', compact('employeePayroll', 'employee', 'user'));
                    $pdfPath = storage_path('app/public/payslips/' . $employeePayroll->id . '.pdf');
                    $pdf->save($pdfPath);

                    Mail::to($user->email)->send(new PayslipMail($employeePayroll, $pdfPath, $user->name));
                }
            }

            $payroll->update(['emailed' => true]);

            return RequestResponse::ok('Payslips emailed successfully.');
        });
    }

    public function emailP9(Request $request, $id)
    {
        return $this->handleTransaction(function () use ($id) {
            $business = Business::findBySlug(session('active_business_slug'));
            if (!$business) {
                return RequestResponse::badRequest('Business not found.');
            }

            $payroll = Payroll::where('business_id', $business->id)
                ->where('id', $id)
                ->with(['employeePayrolls.employee.user'])
                ->firstOrFail();

            foreach ($payroll->employeePayrolls as $employeePayroll) {
                $employee = $employeePayroll->employee;
                $user = $employee->user;

                if ($user && $user->email) {
                    $pdf = Pdf::loadView('payroll.p9', compact('employeePayroll', 'employee', 'user'));
                    $pdfPath = storage_path('app/public/p9/' . $employeePayroll->id . '.pdf');
                    $pdf->save($pdfPath);

                    Mail::to($user->email)->send(new \App\Mail\P9Mail($employeePayroll, $pdfPath));
                }
            }

            return RequestResponse::ok('P9 forms emailed successfully.');
        });
    }

    public function downloadPayroll(Request $request, $id, $format = 'pdf')
    {
        $business = Business::findBySlug(session('active_business_slug'));
        if (!$business) {
            return RequestResponse::badRequest('Business not found.');
        }

        $id = $request->id;
        $format = $request->format;

        $payroll = Payroll::where('business_id', $business->id)
            ->where('id', $id)
            ->with(['employeePayrolls.employee.user'])
            ->firstOrFail();

        // Determine entity (Business or Location)
        $entity = $business;
        $entityType = 'business';
        if ($payroll->location_id) {
            $location = Location::where('id', $payroll->location_id)
                ->where('business_id', $business->id)
                ->first();
            if ($location) {
                $entity = $location;
                $entityType = 'location';
            }
        }

        // Prepare payroll data
        $data = $payroll->employeePayrolls->map(function ($ep) {
            $deductions = json_decode($ep->deductions, true) ?? [];
            return [
                'employee_name' => $ep->employee->user->name ?? 'N/A',
                'employee_code' => $ep->employee->employee_code ?? 'N/A',
                'basic_salary' => (float) $ep->basic_salary,
                'gross_pay' => (float) $ep->gross_pay,
                'overtime' => (float) $ep->overtime,
                'shif' => (float) ($deductions['shif'] ?? 0),
                'nssf' => (float) ($deductions['nssf'] ?? 0),
                'paye' => (float) ($deductions['paye'] ?? 0),
                'housing_levy' => (float) ($deductions['nhdf'] ?? 0),
                'helb' => (float) ($deductions['helb'] ?? 0),
                'loans' => (float) ($deductions['loan_repayment'] ?? 0),
                'advances' => (float) ($deductions['advance_recovery'] ?? 0),
                'net_pay' => (float) $ep->net_pay,
            ];
        })->toArray();

        // Calculate totals correctly
        $totals = array_map('floatval', [
            'totalBasicSalary' => array_sum(array_column($data, 'basic_salary')),
            'totalGrossPay' => array_sum(array_column($data, 'gross_pay')),
            'totalOvertime' => array_sum(array_column($data, 'overtime')),
            'totalShif' => array_sum(array_column($data, 'shif')),
            'totalNssf' => array_sum(array_column($data, 'nssf')),
            'totalPaye' => array_sum(array_column($data, 'paye')),
            'totalHousingLevy' => array_sum(array_column($data, 'housing_levy')),
            'totalHelb' => array_sum(array_column($data, 'helb')),
            'totalLoans' => array_sum(array_column($data, 'loans')),
            'totalAdvances' => array_sum(array_column($data, 'advances')),
            'totalNetPay' => array_sum(array_column($data, 'net_pay')),
        ]);

        $fileName = "payroll-{$id}.{$format}";

        switch ($format) {
            case 'pdf':
                $pdf = Pdf::loadView('payroll.reports.company_payslip', [
                    'business' => $business,
                    'payroll' => $payroll,
                    'entity' => $entity,
                    'entityType' => $entityType,
                    'data' => $data,
                    'totals' => $totals,
                ])->setPaper('a4', 'landscape');
                return $pdf->download($fileName);

            case 'csv':
                $csvData = implode(',', array_keys($data[0] ?? [])) . "\n";
                foreach ($data as $row) {
                    $csvData .= implode(',', array_map('strval', $row)) . "\n";
                }
                return Response::make($csvData, 200, [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
                ]);

            case 'xlsx':
                return Excel::download(new class($data) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
                    private $data;

                    public function __construct(array $data)
                    {
                        $this->data = $data;
                    }

                    public function array(): array
                    {
                        return $this->data;
                    }

                    public function headings(): array
                    {
                        return array_keys($this->data[0] ?? []);
                    }
                }, $fileName);

            default:
                return RequestResponse::badRequest('Invalid format requested.');
        }
    }

    public function printAllPayslips(Request $request, $id)
    {
        $business = Business::findBySlug(session('active_business_slug'));
        if (!$business) {
            return RequestResponse::badRequest('Business not found.');
        }

        $payroll = Payroll::where('business_id', $business->id)
            ->where('id', $id)
            ->with(['employeePayrolls.employee.user'])
            ->firstOrFail();

        $pdf = Pdf::loadView('payroll.all_payslips', compact('payroll'));
        $pdfPath = storage_path('app/public/payslips/all_' . $payroll->id . '.pdf');
        $pdf->save($pdfPath);

        return response()->download($pdfPath, 'all_payslips_' . $payroll->id . '.pdf');
    }

    public function viewPayroll(Request $request, $payroll_id)
    {
        $business = Business::findBySlug(session('active_business_slug'));
        if (!$business) {
            Log::error('viewPayroll: Business not found for slug: ' . (session('active_business_slug') ?? 'Not set'));
            return RequestResponse::badRequest('Business not found.');
        }

        $businessSlug = $request->route('business');
        $payroll_id = $request->id;

        $payroll = Payroll::where('business_id', $business->id)
            ->where('id', $payroll_id)
            ->with(['employeePayrolls.employee.user'])
            ->firstOrFail();

        // Determine the entity (Business or Location)
        $entity = $business;
        $entityType = 'business';
        $page = $entity->company_name . ' Payroll -' . $payroll->payrun_month . ' - ' . $payroll->payrun_year;
        if ($payroll->location_id) {
            $location = Location::where('id', $payroll->location_id)
                ->where('business_id', $business->id)
                ->first();
            if ($location) {
                $entity = $location;
                $entityType = 'location';
                $page = $entity->name . ' Payroll -' . $payroll->payrun_month . ' - ' . $payroll->payrun_year;
            } else {
                Log::warning('viewPayroll: Location not found for payroll ID: ' . $payroll_id);
            }
        }

        // Calculate totals for each column
        $totals = [
            'totalBasicSalary' => 0,
            'totalGrossPay' => 0,
            'totalOvertime' => 0,
            'totalShif' => 0,
            'totalNssf' => 0,
            'totalPaye' => 0,
            'totalHousingLevy' => 0,
            'totalHelb' => 0,
            'totalLoans' => 0,
            'totalAdvances' => 0,
            'totalNetPay' => 0,
        ];

        foreach ($payroll->employeePayrolls as $ep) {
            $deductions = json_decode($ep->deductions, true) ?? [];
            $totals['totalBasicSalary'] += $ep->basic_salary;
            $totals['totalGrossPay'] += $ep->gross_pay;
            $totals['totalOvertime'] += $ep->overtime;
            $totals['totalShif'] += $deductions['shif'] ?? 0;
            $totals['totalNssf'] += $deductions['nssf'] ?? 0;
            $totals['totalPaye'] += $deductions['paye'] ?? 0;
            $totals['totalHousingLevy'] += $deductions['nhdf'] ?? 0;
            $totals['totalHelb'] += $deductions['helb'] ?? 0;
            $totals['totalLoans'] += $deductions['loan_repayment'] ?? 0;
            $totals['totalAdvances'] += $deductions['advance_recovery'] ?? 0;
            $totals['totalNetPay'] += $ep->net_pay;
        }

        return view('payroll.view', compact('payroll', 'business', 'entity', 'entityType', 'totals', 'page'));
    }

    public function downloadColumn(Request $request, $payroll_id, $column, $format)
    {
        $businessSlug = $request->route('business');
        $business = Business::findBySlug($businessSlug);

        $payroll_id = $request->id;
        $column = $request->column;
        $format = $request->format;

        if (!$business) {
            abort(404, 'Business not found.');
        }

        $payroll = Payroll::where('business_id', $business->id)
            ->where('id', $payroll_id)
            ->with(['employeePayrolls.employee.user'])
            ->firstOrFail();

        // Define valid columns
        $validColumns = [
            'basic_salary',
            'gross_pay',
            'overtime',
            'shif',
            'nssf',
            'paye',
            'housing_levy',
            'helb',
            'loans',
            'advances',
            'net_pay',
        ];

        if (!in_array($column, $validColumns)) {
            abort(400, 'Invalid column name.');
        }

        // Prepare data for download
        $data = $payroll->employeePayrolls->map(function ($ep) use ($column) {
            $deductions = json_decode($ep->deductions, true) ?? [];
            $value = match ($column) {
                'basic_salary' => $ep->basic_salary,
                'gross_pay' => $ep->gross_pay,
                'overtime' => $ep->overtime,
                'shif' => $deductions['shif'] ?? 0,
                'nssf' => $deductions['nssf'] ?? 0,
                'paye' => $deductions['paye'] ?? 0,
                'housing_levy' => $deductions['nhdf'] ?? 0,
                'helb' => $deductions['helb'] ?? 0,
                'loans' => $deductions['loan_repayment'] ?? 0,
                'advances' => $deductions['advance_recovery'] ?? 0,
                'net_pay' => $ep->net_pay,
                default => 0,
            };

            return [
                'employee_name' => $ep->employee->user->name ?? 'N/A',
                'employee_code' => $ep->employee->employee_code ?? 'N/A',
                $column => number_format($value, 2),
            ];
        })->toArray();

        // Generate file based on format
        $fileName = "payroll-{$payroll_id}-{$column}." . $format;

        switch ($format) {
            case 'pdf':
                $pdf = Pdf::loadView('payroll.download_column', [
                    'business' => $business,
                    'payroll' => $payroll,
                    'column' => $column,
                    'data' => $data,
                ]);
                return $pdf->download($fileName);

            case 'csv':
                $csvData = "Employee Name,Employee Code," . ucwords(str_replace('_', ' ', $column)) . " (" . ($payroll->currency ?? 'KES') . ")\n";
                foreach ($data as $row) {
                    $csvData .= "\"{$row['employee_name']}\",\"{$row['employee_code']}\",{$row[$column]}\n";
                }
                return Response::make($csvData, 200, [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
                ]);

            case 'xlsx':
                return Excel::download(new class($data, $column, $payroll) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
                    private $data;
                    private $column;
                    private $payroll;

                    public function __construct(array $data, string $column, $payroll)
                    {
                        $this->data = $data;
                        $this->column = $column;
                        $this->payroll = $payroll;
                    }

                    public function array(): array
                    {
                        return $this->data;
                    }

                    public function headings(): array
                    {
                        return [
                            'Employee Name',
                            'Employee Code',
                            ucwords(str_replace('_', ' ', $this->column)) . ' (' . ($this->payroll->currency ?? 'KES') . ')',
                        ];
                    }
                }, $fileName);

            default:
                abort(400, 'Invalid format.');
        }
    }

    public function sendPayslips(Request $request, $id)
    {
        $id = $request->id;
        return $this->handleTransaction(function () use ($id) {
            $business = Business::findBySlug(session('active_business_slug'));
            if (!$business) return RequestResponse::badRequest('Business not found.');

            $payroll = Payroll::where('business_id', $business->id)
                ->where('id', $id)
                ->with(['employeePayrolls.employee.user'])
                ->firstOrFail();

            foreach ($payroll->employeePayrolls as $employeePayroll) {
                if ($employeePayroll->employee->user?->email) {
                    $pdf = Pdf::loadView('payroll.reports.payslip', compact('employeePayroll'));
                    Mail::to($employeePayroll->employee->user->email)->queue(new PayslipMail($employeePayroll, $pdf, $employeePayroll->employee->user->name));
                }
            }

            return RequestResponse::ok('Payslips queued for sending.');
        });
    }

    public function close(Request $request)
    {
        $payroll = Payroll::findOrFail($request->payroll_id);
        $payroll->update(['status' => 'closed']);
        if ($request->carry_forward) {
            $this->carryForward($payroll);
        }
        return RequestResponse::ok('Payroll closed successfully.');
    }

    public function downloadReport(Request $request)
    {
        $validTypes = ['shif', 'nssf', 'paye', 'nhdf', 'tax_filing', 'bank_advice', 'company_payslip'];

        $payroll = Payroll::findOrFail($request->payroll_id);
        $type = $request->type;

        if (!in_array($type, $validTypes)) {
            abort(404, "Invalid report type: {$type}");
        }

        if (!view()->exists("payroll.reports.{$type}")) {
            abort(404, "Report view for {$type} not found");
        }

        $pdf = Pdf::loadView("payroll.reports.{$type}", ['payroll' => $payroll]);
        return $pdf->download("{$type}_report_{$payroll->payrun_year}_{$payroll->payrun_month}.pdf");
    }

    public function addAdjustment(Request $request)
    {
        $business = Business::findBySlug(session('active_business_slug'));
        $type = $request->type;
        $scope = $request->scope;
        $scope_id = $request->scope_id;
        $amount = $request->amount;
        $name = $request->name;

        if (!in_array($type, ['allowance', 'deduction', 'relief'])) {
            return RequestResponse::badRequest('Invalid adjustment type.');
        }

        $model = match ($type) {
            'allowance' => Allowance::class,
            'deduction' => Deduction::class,
            'relief' => Relief::class,
        };

        $adjustment = $model::firstOrCreate(
            ['business_id' => $business->id, 'name' => $name, 'slug' => Str::slug($name)],
            ['amount' => $amount]
        );

        if ($scope === 'employee') {
            $employee = Employee::findOrFail($scope_id);
            $relationshipMethod = $type . 's';
            $pivotTable = $type === 'allowance' ? 'employee_allowances' : ($type === 'deduction' ? 'employee_deductions' : 'employee_reliefs');
            $existingAdjustment = DB::table($pivotTable)
                ->where('employee_id', $employee->id)
                ->where($type . "_id", $adjustment->id)
                ->first();

            if ($existingAdjustment) {
                DB::table($pivotTable)
                    ->where('employee_id', $employee->id)
                    ->where($type . "_id", $adjustment->id)
                    ->update(['amount' => $amount]);
            } else {
                $employee->{$relationshipMethod}()->create([
                    $type . '_id' => $adjustment->id,
                    'amount' => $amount
                ]);
            }
        } elseif ($scope !== 'business') {
            $relation = match ($scope) {
                'location' => 'locations',
                'department' => 'departments',
                'job_category' => 'job_categories',
            };

            $business->$relation()->findOrFail($scope_id)->employees()->each(function ($employee) use ($adjustment, $type, $amount) {
                $relationshipMethod = $type . 's';
                $pivotTable = $type === 'allowance' ? 'employee_allowances' : ($type === 'deduction' ? 'employee_deductions' : 'employee_reliefs');
                $existingAdjustment = DB::table($pivotTable)
                    ->where('employee_id', $employee->id)
                    ->where($type . "_id", $adjustment->id)
                    ->first();

                if ($existingAdjustment) {
                    DB::table($pivotTable)
                        ->where('employee_id', $employee->id)
                        ->where($type . "_id", $adjustment->id)
                        ->update(['amount' => $amount]);
                } else {
                    $employee->{$relationshipMethod}()->create([
                        $type . '_id' => $adjustment->id,
                        'amount' => $amount
                    ]);
                }
            });
        }

        return RequestResponse::ok("{$type} added successfully.");
    }

    private function carryForward(Payroll $payroll)
    {
        $employeePayrolls = EmployeePayroll::where('payroll_id', $payroll->id)->get();
        foreach ($employeePayrolls as $ep) {
            if ($ep->loan_repayment > 0) {
                $remaining = Loan::where('employee_id', $ep->employee_id)->sum(DB::raw('amount - (SELECT COALESCE(SUM(amount), 0) FROM loan_repayments WHERE loan_id = loans.id)'));
                if ($remaining > 0) {
                    Loan::where('employee_id', $ep->employee_id)
                        ->whereRaw('amount > (SELECT COALESCE(SUM(amount), 0) FROM loan_repayments WHERE loan_id = loans.id)')
                        ->first()
                        ->update(['monthly_repayment' => min($remaining, $ep->loan_repayment)]);
                }
            }
            if ($ep->advance_recovery > 0) {
                $remaining = Advance::where('employee_id', $ep->employee_id)->sum('amount');
                if ($remaining > 0) {
                    Advance::where('employee_id', $ep->employee_id)
                        ->where('amount', '>', 0)
                        ->first()
                        ->update(['amount' => max(0, $remaining - $ep->advance_recovery)]);
                }
            }
        }
    }

    public function filter(Request $request)
    {
        $business = Business::findBySlug(session('active_business_slug'));
        if (!$business) {
            return RequestResponse::badRequest('Business not found.');
        }

        $query = Payroll::where('business_id', $business->id)
            ->withCount(['employeePayrolls as no_of_payslips'])
            ->with(['employeePayrolls' => function ($query) {
                $query->select('payroll_id', 'net_pay');
            }]);

        if ($request->filled('month')) {
            $query->where('payrun_month', $request->input('month'));
        }

        if ($request->filled('year')) {
            $query->where('payrun_year', $request->input('year'));
        }

        if ($request->filled('location')) {
            $query->whereHas('employeePayrolls.employee', function ($q) use ($request) {
                $q->where('location_id', $request->input('location'));
            });
        }

        if ($request->filled('department')) {
            $query->whereHas('employeePayrolls.employee.employmentDetails', function ($q) use ($request) {
                $q->where('department_id', $request->input('department'));
            });
        }

        if ($request->filled('job_category')) {
            $query->whereHas('employeePayrolls.employee.employmentDetails', function ($q) use ($request) {
                $q->where('job_category_id', $request->input('job_category'));
            });
        }

        if ($request->filled('employee')) {
            $query->whereHas('employeePayrolls.employee', function ($q) use ($request) {
                $q->where('id', $request->input('employee'));
            });
        }

        $payrolls = $query->get();

        $pastPayrollsTable = view('payroll._past', compact('payrolls', 'business'))->render();
        return RequestResponse::ok('Payrolls filtered successfully.', ['html' => $pastPayrollsTable]);
    }
}
