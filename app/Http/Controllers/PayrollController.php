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
use App\Models\PayrollFormulaBracket;
use App\Models\EmployeePayrollDetail;

class PayrollController extends Controller
{
    use HandleTransactions;

    public function index(Request $request)
    {
        $business = Business::findBySlug(session('active_business_slug'));
        if (!$business) return RequestResponse::badRequest('Business not found.');

        $locations = $business->locations->prepend((object) [
            'id' => 'business_' . $business->id,
            'name' => $business->name . ' (All Locations)',
        ]);

        return view('payroll.index', [
            'page' => 'Process Payroll',
            'years' => range(date('Y'), date('Y') + 5),
            'months' => range(1, 12),
            'locations' => $locations,
            'departments' => $business->departments,
            'jobCategories' => $business->job_categories,
            'employees' => Employee::where('business_id', $business->id)->with('user')->get(['id', 'user_id']),
            'allowances' => $business->allowances,
            'deductions' => $business->deductions,
            'reliefs' => $business->reliefs,
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

        // Ensure exempted_employees is an array before filtering
        $exemptedEmployees = $options['exempted_employees'] ?? [];
        $nonExemptedEmployees = $employees->filter(fn($e) => !array_key_exists($e->id, $exemptedEmployees));
        $daysInMonth = Carbon::create($request->year, $request->month)->daysInMonth;

        return RequestResponse::ok('Employees fetched successfully.', [
            'html' => view('payroll._table', [
                'employees' => $nonExemptedEmployees,
                'warnings' => $warnings,
                'options' => $options,
                'allowances' => $business->allowances,
                'deductions' => $business->deductions,
                'reliefs' => $business->reliefs,
                'daysInMonth' => $daysInMonth,
            ])->render(),
            'count' => $nonExemptedEmployees->count(),
            'warnings' => $warnings,
            'options' => $options,
        ]);
    }

    public function preview(Request $request)
    {
        $business = Business::findBySlug(session('active_business_slug'));
        $employees = $this->getFilteredEmployees($request, $business);
        $warnings = $this->checkMissingData($employees);
        $options = $this->parseOptions($request);

        if (!empty($warnings)) {
            return RequestResponse::badRequest('Resolve warnings before previewing.', ['warnings' => $warnings]);
        }

        $nonExemptedEmployees = $employees->filter(fn($e) => !array_key_exists($e->id, $options['exempted_employees']));
        $payrollData = $this->calculatePayroll($nonExemptedEmployees, $request->year, $request->month, $options);

        return RequestResponse::ok('Payroll preview generated.', [
            'html' => view('payroll._preview', compact('payrollData', 'options'))->render(),
            'options' => $options,
        ]);
    }

    public function store(Request $request)
    {
        $business = Business::findBySlug(session('active_business_slug'));
        $employees = $this->getFilteredEmployees($request, $business);
        $options = $this->parseOptions($request);

        return $this->handleTransaction(function () use ($request, $business, $employees, $options) {
            $nonExemptedEmployees = $employees->filter(fn($e) => !array_key_exists($e->id, $options['exempted_employees']));
            $payroll = Payroll::create([
                'payrun_year' => $request->year,
                'payrun_month' => $request->month,
                'business_id' => $business->id,
                'location_id' => str_starts_with($request->location_id, 'business_') ? null : $request->location_id,
                'payroll_type' => 'monthly',
                'status' => 'open',
                'staff' => $nonExemptedEmployees->count(),
                'currency' => $business->currency ?? 'KES',
            ]);

            $payrollData = $this->calculatePayroll($nonExemptedEmployees, $request->year, $request->month, $options);

            foreach ($payrollData as $data) {
                EmployeePayroll::create([
                    'payroll_id' => $payroll->id,
                    'employee_id' => $data['employee_id'],
                    'basic_salary' => $data['basic_salary'],
                    'gross_pay' => $data['gross_pay'],
                    'overtime' => $data['overtime'],
                    'shif' => $data['shif'],
                    'nssf' => $data['nssf'],
                    'paye' => $data['paye'],
                    'housing_levy' => $data['housing_levy'],
                    'taxable_income' => $data['taxable_income'],
                    'personal_relief' => $data['personal_relief'],
                    'loan_repayment' => $data['loan_repayment'],
                    'advance_recovery' => $data['advance_recovery'],
                    'deductions_after_tax' => $data['deductions_after_tax'],
                    'net_pay' => $data['net_pay'],
                    'deductions' => json_encode(array_merge($data['deductions'], [
                        'shif' => $data['shif'],
                        'nssf' => $data['nssf'],
                        'paye' => $data['paye'],
                        'housing_levy' => $data['housing_levy'],
                        'helb' => $data['helb'],
                    ])),
                    'allowances' => json_encode($data['allowances']),
                    'reliefs' => json_encode($data['reliefs']),
                    'employee_payroll_detail_id' => $data['employee']->payrollDetail->id ?? null,
                ]);

                $this->updateLoanAndAdvance($data, $request->year, $request->month, $options);
            }

            return RequestResponse::ok('Payroll processed successfully.', [
                'redirect_url' => route('business.payroll.view', ['business' => $business->slug, 'id' => $payroll->id])
            ]);
        }, function ($e) {
            Log::error('Payroll store failed: ' . $e->getMessage());
            return RequestResponse::badRequest('Failed to process payroll: ' . $e->getMessage());
        });
    }

    public function addAdjustment(Request $request)
    {
        $business = Business::findBySlug(session('active_business_slug'));
        $employee = Employee::findOrFail($request->employee_id);

        $options = $this->parseOptions($request);

        if ($request->allowances) {
            foreach ($request->allowances as $allowanceId) {
                $allowance = Allowance::find($allowanceId);
                if ($allowance) {
                    EmployeeAllowance::updateOrCreate(
                        ['employee_id' => $employee->id, 'allowance_id' => $allowanceId],
                        ['is_active' => true]
                    );
                }
            }
        }

        if ($request->deductions) {
            foreach ($request->deductions as $deductionId) {
                $deduction = Deduction::find($deductionId);
                if ($deduction) {
                    EmployeeDeduction::updateOrCreate(
                        ['employee_id' => $employee->id, 'deduction_id' => $deductionId],
                        ['is_active' => true]
                    );
                }
            }
        }

        if ($request->reliefs) {
            foreach ($request->reliefs as $reliefId) {
                $relief = Relief::find($reliefId);
                if ($relief) {
                    EmployeeRelief::updateOrCreate(
                        ['employee_id' => $employee->id, 'relief_id' => $reliefId],
                        ['is_active' => true]
                    );
                }
            }
        }

        // Handle loan and advance recovery adjustments
        if ($request->loans) {
            foreach ($request->loans as $loanId => $amount) {
                $loan = Loan::find($loanId);
                if ($loan && $amount > 0) {
                    $options['recover_loans']['specific'][$loanId] = min(floatval($amount), $loan->amount - $loan->repayments->sum('amount'));
                }
            }
        }

        if ($request->advances) {
            foreach ($request->advances as $advanceId => $amount) {
                $advance = Advance::find($advanceId);
                if ($advance && $amount > 0) {
                    $options['recover_advances']['specific'][$advanceId] = min(floatval($amount), $advance->amount);
                }
            }
        }

        if ($request->overtime) {
            foreach ($request->overtime as $overtimeId => $value) {
                $overtime = Overtime::find($overtimeId);
                if ($overtime && $value) {
                    $options['pay_overtime']['specific'][$overtimeId] = true;
                }
            }
        }

        $employee->load('employeeAllowances.allowance', 'employeeDeductions.deduction', 'loans', 'advances', 'overtimes');
        return RequestResponse::ok('success', [
            'allowances' => $employee->employeeAllowances->map(fn($ea) => $ea->allowance ? "{$ea->allowance->name} (" . number_format($ea->allowance->amount ?? 0, 2) . ")" : null)->filter()->toArray(),
            'deductions' => $employee->employeeDeductions->map(fn($ed) => $ed->deduction ? "{$ed->deduction->name} (" . number_format($ed->deduction->amount ?? 0, 2) . ")" : null)->filter()->toArray(),
            'loans' => $employee->loans->map(fn($l) => ['id' => $l->id, 'amount' => $l->amount, 'remaining' => $l->amount - $l->repayments->sum('amount')])->toArray(),
            'advances' => $employee->advances->map(fn($a) => ['id' => $a->id, 'date' => $a->date?->format('Y-m-d'), 'amount' => $a->amount])->toArray(),
            'overtimes' => $employee->overtimes->map(fn($o) => ['id' => $o->id, 'hours' => $o->overtime_hours, 'rate' => $o->rate, 'total_pay' => $o->total_pay])->toArray(),
            'options' => $options,
        ]);
    }

    protected function getFilteredEmployees(Request $request, Business $business)
    {
        $query = Employee::where('business_id', $business->id)
            ->with([
                'user',
                'paymentDetails',
                'employmentDetails', // Ensure this is loaded
                'employeeAllowances.allowance',
                'employeeDeductions.deduction',
                'reliefs' => fn($q) => $q->withPivot('amount', 'is_active', 'start_date', 'end_date'),
                'overtimes' => fn($q) => $q->whereYear('date', $request->year)->whereMonth('date', $request->month),
                'advances' => fn($q) => $q->whereYear('date', $request->year)->whereMonth('date', $request->month),
                'loans.repayments' => fn($q) => $q->where('start_date', '<=', Carbon::create($request->year, $request->month)->endOfMonth())
                    ->where('end_date', '>=', Carbon::create($request->year, $request->month)->startOfMonth()),
                'attendances' => fn($q) => $q->whereYear('date', $request->year)->whereMonth('date', $request->month),
            ]);

        if ($request->location_id && !str_starts_with($request->location_id, 'business_')) {
            $query->where('location_id', $request->location_id);
        }
        if ($request->department_id) {
            $query->where('department_id', $request->department_id);
        }
        if ($request->job_category_id) {
            $query->whereHas('employmentDetails', function ($q) use ($request) {
                $q->where('job_category_id', $request->job_category_id);
            });
        }

        return $query->get();
    }

    public function getEmployeeAdjustments(Request $request)
    {
        $employeeId = $request->input('employee_id');
        if (!$employeeId) {
            return RequestResponse::badRequest('Employee ID is required.');
        }

        $employee = Employee::with(['loans.repayments', 'advances', 'overtimes'])
            ->findOrFail($employeeId);

        return RequestResponse::ok('Employee data fetched.', [
            'data' => [
                'loans' => $employee->loans->map(function ($loan) {
                    return [
                        'id' => $loan->id,
                        'amount' => $loan->amount ?? 0,
                        'repayments' => $loan->repayments->map(fn($r) => ['amount' => $r->amount ?? 0])->toArray(),
                    ];
                })->toArray(),
                'advances' => $employee->advances->map(function ($advance) {
                    return [
                        'id' => $advance->id,
                        'date' => $advance->date?->format('Y-m-d'),
                        'amount' => $advance->amount ?? 0,
                    ];
                })->toArray(),
                'overtimes' => $employee->overtimes->map(function ($overtime) {
                    return [
                        'id' => $overtime->id,
                        'date' => $overtime->date?->format('Y-m-d'),
                        'total_pay' => $overtime->total_pay ?? 0,
                    ];
                })->toArray(),
            ]
        ]);
    }

    protected function checkMissingData($employees)
    {
        $warnings = [];
        foreach ($employees as $employee) {
            if (!$employee->paymentDetails) $warnings[$employee->id][] = 'Missing payment details';
            if (!$employee->tax_no) $warnings[$employee->id][] = 'Missing KRA PIN';
            if (!$employee->user || !$employee->user->email) $warnings[$employee->id][] = 'Missing email';
        }
        return $warnings;
    }

    protected function parseOptions(Request $request)
    {
        $exempted = $request->input('exempted_employees', []);
        if (is_string($exempted)) {
            $exempted = json_decode($exempted, true) ?? [];
        }

        return [
            'exempted_employees' => $exempted,
            'recover_advances' => [
                'apply' => $request->input('recover_advances', 'none'),
                'specific' => $request->input('recover_advances_specific', [])
            ],
            'recover_loans' => [
                'apply' => $request->input('recover_loans', 'none'),
                'specific' => $request->input('recover_loans_specific', [])
            ],
            'pay_overtime' => [
                'apply' => $request->input('pay_overtime', 'none'),
                'specific' => $request->input('pay_overtime_specific', [])
            ],
        ];
    }

    protected function calculatePayroll($employees, $year, $month, $options)
    {
        $business = Business::findBySlug(session('active_business_slug'));
        $payrollData = [];
        $period = Carbon::create($year, $month);
        $daysInMonth = $period->daysInMonth;

        foreach ($employees as $employee) {
            $employeeId = $employee->id;
            $payrollDetail = EmployeePayrollDetail::where('employee_id', $employeeId)->first();
            $basicSalary = $employee->paymentDetails->basic_salary ?? 0;

            $allowances = $employee->employeeAllowances->mapWithKeys(fn($ea) => [
                $ea->allowance->id => [
                    'name' => $ea->allowance->name,
                    'amount' => $ea->amount ?? 0,
                    'is_taxable' => $ea->allowance->is_taxable ?? true,
                ]
            ])->toArray();
            $totalTaxableAllowances = array_sum(array_map(fn($a) => $a['is_taxable'] ? $a['amount'] : 0, $allowances));
            $totalNonTaxableAllowances = array_sum(array_map(fn($a) => !$a['is_taxable'] ? $a['amount'] : 0, $allowances));

            $overtime = $this->calculateOvertime($employeeId, $year, $month, $options);
            $grossPay = $basicSalary + $totalTaxableAllowances + $totalNonTaxableAllowances + $overtime;

            $shif = $this->calculateStatutoryDeduction($business->id, 'SHIF', $grossPay);
            $nssf = $this->calculateStatutoryDeduction($business->id, 'NSSF', $grossPay);
            $nhdf = $this->calculateStatutoryDeduction($business->id, 'NHDF', $grossPay);
            $helb = $this->calculateHelb($employeeId, $grossPay, $business->id);

            $deductions = $employee->employeeDeductions->mapWithKeys(fn($ed) => [
                $ed->deduction->id => [
                    'name' => $ed->deduction->name,
                    'amount' => $ed->amount ?? 0,
                ]
            ])->toArray();
            $totalCustomDeductions = array_sum(array_map(fn($d) => $d['amount'], $deductions));

            $reliefs = $this->calculateReliefs($employee, $period);
            $personalRelief = min(2400, $reliefs['personal-relief']['amount'] ?? $this->calculateStatutoryDeduction($business->id, 'Personal Relief', $grossPay, true));
            $insuranceRelief = $payrollDetail && $payrollDetail->has_insurance ? min(15000, $payrollDetail->insurance_premium * 0.15) : 0;
            $mortgageRelief = $payrollDetail && $payrollDetail->has_mortgage ? min(25000, $payrollDetail->mortgage_interest) : 0;
            $hospRelief = $payrollDetail && $payrollDetail->has_hosp ? min(3600, $payrollDetail->hosp_deposit) : 0;

            $taxableIncome = max(0, $grossPay - $nssf);
            $paye = $this->calculatePAYE($business->id, $taxableIncome) - ($personalRelief + $insuranceRelief + $mortgageRelief + $hospRelief);
            $paye = max(0, $paye);

            $presentDays = $employee->attendances->where('is_absent', false)->count();
            $absentDays = $daysInMonth - $presentDays;

            $loanRepayment = $this->calculateLoanRepayment($employeeId, $year, $month, $options);
            $advanceRecovery = $this->calculateAdvanceRecovery($employeeId, $year, $month, $options);

            $deductionsAfterTax = $shif + $nssf + $nhdf + $helb + $paye + $totalCustomDeductions + $loanRepayment + $advanceRecovery;
            $netPay = $grossPay - $deductionsAfterTax;

            $payrollData[$employeeId] = [
                'employee_id' => $employeeId,
                'employee' => $employee,
                'basic_salary' => $basicSalary,
                'gross_pay' => $grossPay,
                'overtime' => $overtime,
                'shif' => $shif,
                'nssf' => $nssf,
                'paye' => $paye,
                'housing_levy' => $nhdf,
                'helb' => $helb,
                'taxable_income' => $taxableIncome,
                'personal_relief' => $personalRelief,
                'insurance_relief' => $insuranceRelief,
                'mortgage_relief' => $mortgageRelief,
                'hosp_relief' => $hospRelief,
                'loan_repayment' => $loanRepayment,
                'advance_recovery' => $advanceRecovery,
                'deductions_after_tax' => $deductionsAfterTax,
                'net_pay' => $netPay,
                'deductions' => $deductions,
                'allowances' => $allowances,
                'reliefs' => array_merge($reliefs, [
                    'insurance-relief' => ['name' => 'Insurance Relief', 'amount' => $insuranceRelief, 'tax_application' => 'after_tax'],
                    'mortgage-relief' => ['name' => 'Mortgage Relief', 'amount' => $mortgageRelief, 'tax_application' => 'after_tax'],
                    'hosp-relief' => ['name' => 'HOSP Relief', 'amount' => $hospRelief, 'tax_application' => 'after_tax'],
                ]),
                'bank_name' => $employee->paymentDetails->bank_name ?? 'N/A',
                'account_number' => $employee->paymentDetails->account_number ?? 'N/A',
                'currency' => $employee->paymentDetails->currency ?? 'KES',
                'payment_mode' => $employee->paymentDetails->payment_mode ?? 'N/A',
                'attendance_present' => $presentDays,
                'attendance_absent' => $absentDays,
            ];
        }

        return $payrollData;
    }

    protected function calculateStatutoryDeduction($businessId, $name, $amount, $isRelief = false)
    {
        $formula = PayrollFormula::where('business_id', $businessId)->where('name', $name)->first();
        if (!$formula) {
            return $isRelief ? 2400 : 0;
        }

        if ($formula->formula_type === 'fixed') {
            return $formula->minimum_amount ?? 0;
        }

        if ($formula->is_progressive) {
            $bracket = PayrollFormulaBracket::where('payroll_formula_id', $formula->id)
                ->where('min', '<=', $amount)
                ->where(function ($query) use ($amount) {
                    $query->where('max', '>=', $amount)->orWhereNull('max');
                })
                ->first();

            if ($bracket) {
                if ($bracket->amount) return $bracket->amount;
                if ($bracket->rate) return $amount * ($bracket->rate / 100);
            }
        }

        return $amount * (($formula->minimum_amount ?? 0) / 100);
    }

    protected function calculateOvertime($employeeId, $year, $month, $options)
    {
        if ($options['pay_overtime']['apply'] === 'none') return 0;

        $employee = Employee::with('paymentDetails')->find($employeeId);
        if (!$employee || !$employee->paymentDetails) return 0;

        $basicSalary = $employee->paymentDetails->basic_salary ?? 0;
        $hourlyRate = $basicSalary / 160; // Standard 160 hours per month

        $overtimes = Overtime::where('employee_id', $employeeId)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get();

        $totalOvertimePay = 0;

        foreach ($overtimes as $overtime) {
            if (isset($options['pay_overtime']['specific'][$overtime->id])) {
                // Calculate total_pay using rate from database
                $computedTotalPay = $overtime->overtime_hours * $hourlyRate * $overtime->rate;
                $overtime->update(['total_pay' => $computedTotalPay]);
                $totalOvertimePay += $computedTotalPay;
            }
        }

        return $totalOvertimePay;
    }

    protected function calculateAdvanceRecovery($employeeId, $year, $month, $options)
    {
        if ($options['recover_advances']['apply'] === 'none') return 0;

        return Advance::where('employee_id', $employeeId)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->sum('amount') ?? 0;
    }

    protected function calculateLoanRepayment($employeeId, $year, $month, $options)
    {
        if ($options['recover_loans']['apply'] === 'none') return 0;

        $period = Carbon::create($year, $month);
        $loans = Loan::where('employee_id', $employeeId)
            ->where('start_date', '<=', $period->endOfMonth())
            ->where('end_date', '>=', $period->startOfMonth())
            ->get();

        $totalRepayment = 0;
        foreach ($loans as $loan) {
            $monthly = $loan->amount / $loan->term_months;
            $repaid = $loan->repayments->sum('amount');
            $remaining = max(0, $loan->amount - $repaid);
            if ($remaining > 0) $totalRepayment += min($monthly, $remaining);
        }

        return $totalRepayment;
    }

    protected function calculatePAYE($businessId, $taxableIncome)
    {
        $formula = PayrollFormula::where('business_id', $businessId)->where('name', 'PAYE')->first();
        if (!$formula || !$formula->is_progressive) return 0;

        $brackets = PayrollFormulaBracket::where('payroll_formula_id', $formula->id)
            ->orderBy('min')
            ->get();

        $tax = 0;
        $remainingIncome = $taxableIncome;

        foreach ($brackets as $bracket) {
            $min = $bracket->min;
            $max = $bracket->max ?? PHP_INT_MAX;
            $rate = $bracket->rate / 100;

            if ($remainingIncome <= 0) break;

            $taxableInBracket = min($remainingIncome, $max - $min + 1);
            $tax += $taxableInBracket * $rate;
            $remainingIncome -= $taxableInBracket;
        }

        return $tax;
    }

    protected function calculateHelb($employeeId, $grossPay, $businessId)
    {
        $payrollDetail = EmployeePayrollDetail::where('employee_id', $employeeId)->first();
        if (!$payrollDetail || !$payrollDetail->has_helb) return 0;

        return $this->calculateStatutoryDeduction($businessId, 'HELB', $grossPay);
    }

    protected function calculateReliefs($employee, $period)
    {
        return $employee->reliefs->mapWithKeys(function ($relief) use ($period) {
            $pivot = $relief->pivot;
            if ($pivot->is_active && (!$pivot->start_date || $pivot->start_date <= $period->endOfMonth()) && (!$pivot->end_date || $pivot->end_date >= $period->startOfMonth())) {
                return [$relief->name => [
                    'name' => $relief->name,
                    'amount' => $pivot->amount ?? $relief->amount ?? 0,
                    'tax_application' => $relief->tax_application ?? 'before_tax',
                ]];
            }
            return [];
        })->filter()->toArray();
    }

    protected function updateLoanAndAdvance($payrollData, $year, $month, $options)
    {
        $employeeId = $payrollData['employee_id'];

        if ($payrollData['advance_recovery'] > 0 && $options['recover_advances']['apply'] !== 'none') {
            $advances = Advance::where('employee_id', $employeeId)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->where('amount', '>', 0)
                ->orderBy('date')
                ->get();
            $remainingRecovery = $payrollData['advance_recovery'];
            foreach ($advances as $advance) {
                if ($remainingRecovery <= 0) break;
                $recovery = min($advance->amount, $remainingRecovery);
                $advance->update(['amount' => max(0, $advance->amount - $recovery)]);
                $remainingRecovery -= $recovery;
            }
        }

        if ($payrollData['loan_repayment'] > 0 && $options['recover_loans']['apply'] !== 'none') {
            $period = Carbon::create($year, $month);
            $loans = Loan::where('employee_id', $employeeId)
                ->where('start_date', '<=', $period->endOfMonth())
                ->where('end_date', '>=', $period->startOfMonth())
                ->orderBy('start_date')
                ->get();
            $remainingRepayment = $payrollData['loan_repayment'];
            foreach ($loans as $loan) {
                if ($remainingRepayment <= 0) break;
                $repaid = $loan->repayments->sum('amount');
                $remainingBalance = max(0, $loan->amount - $repaid);
                if ($remainingBalance > 0) {
                    $repayment = min($remainingBalance, $remainingRepayment);
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
            $payroll->update(['status' => "published"]);

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
            $payroll->update(['status' => "unpublished"]);

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

        // Get payroll ID from route parameter (assuming it's passed correctly)
        $payroll = Payroll::where('business_id', $business->id)
            ->where('id', $payroll_id)
            ->with([
                'employeePayrolls.employee.user',
                'employeePayrolls.employee.paymentDetails',
                'employeePayrolls.employee.payrollDetail' // Include payroll details for reliefs
            ])
            ->firstOrFail();

        // Determine the entity (Business or Location)
        $entity = $business;
        $entityType = 'business';
        $page = $entity->company_name . ' Payroll - ' . $payroll->payrun_month . ' - ' . $payroll->payrun_year;
        if ($payroll->location_id) {
            $location = Location::where('id', $payroll->location_id)
                ->where('business_id', $business->id)
                ->first();
            if ($location) {
                $entity = $location;
                $entityType = 'location';
                $page = $entity->name . ' Payroll - ' . $payroll->payrun_month . ' - ' . $payroll->payrun_year;
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
            'totalAbsenteeismCharge' => 0,
            'totalCustomDeductions' => 0,
            'totalPersonalRelief' => 0,
            'totalInsuranceRelief' => 0,
            'totalMortgageRelief' => 0,
            'totalHospRelief' => 0,
            'totalNetPay' => 0,
        ];

        foreach ($payroll->employeePayrolls as $ep) {
            $deductions = json_decode($ep->deductions, true) ?? [];
            $allowances = json_decode($ep->allowances, true) ?? [];
            $reliefs = json_decode($ep->reliefs, true) ?? [];

            // Direct fields from employee_payrolls
            $totals['totalBasicSalary'] += $ep->basic_salary ?? 0;
            $totals['totalGrossPay'] += $ep->gross_pay ?? 0;
            $totals['totalOvertime'] += $ep->overtime ?? 0;
            $totals['totalShif'] += $ep->shif ?? ($deductions['shif'] ?? 0);
            $totals['totalNssf'] += $ep->nssf ?? ($deductions['nssf'] ?? 0);
            $totals['totalPaye'] += $ep->paye ?? ($deductions['paye'] ?? 0);
            $totals['totalHousingLevy'] += $ep->housing_levy ?? ($deductions['housing_levy'] ?? 0); // Use housing_levy
            $totals['totalHelb'] += $deductions['helb'] ?? 0;
            $totals['totalLoans'] += $ep->loan_repayment ?? ($deductions['loan_repayment'] ?? 0);
            $totals['totalAdvances'] += $ep->advance_recovery ?? ($deductions['advance_recovery'] ?? 0);
            $totals['totalAbsenteeismCharge'] += $deductions['absenteeism_charge'] ?? 0;
            $totals['totalNetPay'] += $ep->net_pay ?? 0;
            $totals['totalPersonalRelief'] += $ep->personal_relief ?? ($reliefs['personal-relief']['amount'] ?? 0);

            // Custom deductions (excluding statutory ones already counted)
            $customDeductions = array_filter($deductions, fn($key) => !in_array($key, ['shif', 'nssf', 'paye', 'housing_levy', 'helb', 'loan_repayment', 'advance_recovery', 'absenteeism_charge']), ARRAY_FILTER_USE_KEY);
            $totals['totalCustomDeductions'] += array_sum(array_column($customDeductions, 'amount'));

            // Additional reliefs
            $totals['totalInsuranceRelief'] += $reliefs['insurance-relief']['amount'] ?? 0;
            $totals['totalMortgageRelief'] += $reliefs['mortgage-relief']['amount'] ?? 0;
            $totals['totalHospRelief'] += $reliefs['hosp-relief']['amount'] ?? 0;
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

    public function sendPayslips(Request $request)
    {
        $payrollId = $request->input('payroll_id');
        if (!$payrollId) {
            return RequestResponse::badRequest('Payroll ID is required.');
        }

        return $this->handleTransaction(function () use ($payrollId) {
            $business = Business::findBySlug(session('active_business_slug'));
            if (!$business) {
                return RequestResponse::badRequest('Business not found.');
            }

            $payroll = Payroll::where('business_id', $business->id)
                ->where('id', $payrollId)
                ->with(['employeePayrolls.employee.user'])
                ->firstOrFail();

            foreach ($payroll->employeePayrolls as $employeePayroll) {
                if ($employeePayroll->employee->user?->email) {
                    // Generate the PDF and save it temporarily
                    $pdf = Pdf::loadView('payroll.reports.payslip', compact('employeePayroll'));
                    $fileName = 'payslip_' . $employeePayroll->id . '_' . time() . '.pdf';
                    $filePath = storage_path('app/public/payslips/' . $fileName);

                    // Ensure the directory exists
                    if (!file_exists(storage_path('app/public/payslips'))) {
                        mkdir(storage_path('app/public/payslips'), 0755, true);
                    }

                    // Save the PDF to disk
                    $pdf->save($filePath);

                    // Queue the mail with the file path
                    Mail::to($employeePayroll->employee->user->email)
                        ->queue(new PayslipMail($employeePayroll, $filePath, $employeePayroll->employee->user->name));
                }
            }

            // Update the emailed status
            $payroll->emailed = true;
            $payroll->save();

            return RequestResponse::ok('Payslips queued for sending.');
        }, function ($e) {
            Log::error('Failed to send payslips: ' . $e->getMessage());
            return RequestResponse::badRequest('Failed to send payslips: ' . $e->getMessage());
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
