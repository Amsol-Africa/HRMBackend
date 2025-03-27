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
use App\Models\PayrollSettings;
use App\Models\EmployeePaymentDetail;
use Illuminate\Support\Facades\Http;

use function Illuminate\Log\log;

class PayrollController extends Controller
{
    use HandleTransactions;

    public function index(Request $request)
    {
        $business = Business::findBySlug(session('active_business_slug'));
        if (!$business) return RequestResponse::badRequest('Business not found.');

        $locations = $business->locations->prepend((object) [
            'id' => 'business_' . $business->id,
            'name' => $business->company_name,
        ]);

        $years = range(date('Y') + 5, date('Y'));

        $employees = Employee::where('business_id', $business->id)
            ->with(['user', 'location'])
            ->get(['id', 'user_id', 'location_id', 'employee_code']);

        return view('payroll.index', [
            'page' => 'Process Payroll',
            'years' => $years,
            'months' => range(1, 12),
            'locations' => $locations,
            'departments' => $business->departments,
            'jobCategories' => $business->job_categories,
            'employees' => $employees,
            'allowances' => $business->allowances,
            'deductions' => $business->deductions,
            'reliefs' => $business->reliefs,
            'business' => $business,
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
        $nonExemptedEmployees = $employees->filter(fn($e) => !array_key_exists($e->id, $options['exempted_employees']));
        $daysInMonth = Carbon::create($request->year, $request->month)->daysInMonth;

        $years = range(date('Y') + 5, date('Y'));

        return RequestResponse::ok('success', [
            'html' => view('payroll._table', [
                'employees' => $employees,
                'warnings' => $warnings,
                'options' => $options,
                'allowances' => $business->allowances,
                'deductions' => $business->deductions,
                'reliefs' => $business->reliefs,
                'daysInMonth' => $daysInMonth,
                'years' => $years,
                'business' => $business,
            ])->render(),
            'count' => $nonExemptedEmployees->count(),
            'warnings' => $warnings,
            'options' => $options,
            'years' => $years,
            'employees' => $employees->map(function ($employee) use ($business) {
                return [
                    'id' => $employee->id,
                    'user_id' => $employee->user_id,
                    'name' => $employee->user?->name ?? 'N/A',
                    'location' => $employee->location?->name ?? $business->name,
                    'location_id' => $employee->location_id ?? 'business_' . $business->id,
                    'employee_code' => $employee->employee_code ?? 'N/A',
                    'department' => $employee->employmentDetails?->department?->name ?? 'N/A',
                    'job_category' => $employee->employmentDetails?->jobCategory?->name ?? 'N/A',
                ];
            })->values()->toArray(),
        ]);
    }

    public function availableItems(Request $request)
    {
        $business = Business::findBySlug(session('active_business_slug'));
        if (!$business) return RequestResponse::badRequest('Business not found.');

        $employeeIds = Employee::where('business_id', $business->id)->pluck('id')->toArray();

        $allowances = Allowance::where('business_id', $business->id)
            ->get(['id', 'name', 'amount', 'rate', 'type', 'calculation_basis'])
            ->map(function ($allowance) {
                return [
                    'id' => $allowance->id,
                    'name' => $allowance->name,
                    'amount' => $allowance->amount ?? 0,
                    'rate' => $allowance->rate ?? 0,
                    'type' => $allowance->type ?? 'fixed',
                    'calculation_basis' => $allowance->calculation_basis ?? null,
                ];
            })->toArray();

        $deductions = Deduction::where('business_id', $business->id)
            ->get(['id', 'name', 'amount', 'rate', 'type', 'calculation_basis'])
            ->map(function ($deduction) {
                return [
                    'id' => $deduction->id,
                    'name' => $deduction->name,
                    'amount' => $deduction->amount ?? 0,
                    'rate' => $deduction->rate ?? 0,
                    'type' => $deduction->type ?? 'fixed',
                    'calculation_basis' => $deduction->calculation_basis ?? null,
                ];
            })->toArray();

        $reliefs = Relief::where('business_id', $business->id)
            ->get(['id', 'name', 'amount'])
            ->map(function ($relief) {
                return [
                    'id' => $relief->id,
                    'name' => $relief->name,
                    'amount' => $relief->amount ?? 0,
                ];
            })->toArray();

        $loans = Loan::whereIn('employee_id', $employeeIds)
            ->with('repayments')
            ->get(['id', 'employee_id', 'start_date', 'amount'])
            ->map(function ($loan) {
                $totalRepayments = $loan->repayments->sum('amount') ?? 0;
                $remaining = $loan->amount - $totalRepayments;
                return [
                    'id' => $loan->id,
                    'employee_id' => $loan->employee_id,
                    'start_date' => $loan->start_date?->format('Y-m-d'),
                    'amount' => $loan->amount ?? 0,
                    'remaining' => $remaining > 0 ? $remaining : 0,
                ];
            })
            ->filter(fn($loan) => $loan['remaining'] > 0)
            ->values()
            ->toArray();

        $advances = Advance::whereIn('employee_id', $employeeIds)
            ->get(['id', 'employee_id', 'date', 'amount'])
            ->map(function ($advance) {
                return [
                    'id' => $advance->id,
                    'employee_id' => $advance->employee_id,
                    'date' => $advance->date?->format('Y-m-d'),
                    'amount' => $advance->amount ?? 0,
                    'is_active' => false,
                ];
            })->toArray();

        // \Log::info($deductions);
        // \Log::info($reliefs);
        // \Log::info($loans);
        // \Log::info($advances);

        return RequestResponse::ok('success', [
            'allowances' => $allowances,
            'deductions' => $deductions,
            'reliefs' => $reliefs,
            'loans' => $loans,
            'advances' => $advances,
        ]);
    }

    public function defaultAmount(Request $request, $type, $itemId)
    {
        $business = Business::findBySlug(session('active_business_slug'));
        if (!$business) return RequestResponse::badRequest('Business not found.');

        $modelMap = [
            'allowances' => Allowance::class,
            'deductions' => Deduction::class,
            'reliefs' => Relief::class,
            'loans' => Loan::class,
            'advances' => Advance::class,
        ];

        if (!isset($modelMap[$type])) return RequestResponse::badRequest('Invalid item type.');

        $model = $modelMap[$type];
        $item = $model::find($itemId);
        if (!$item) return RequestResponse::badRequest('Item not found.');

        $amount = $type === 'loans' ? ($item->amount - ($item->repayments->sum('amount') ?? 0)) : ($item->amount ?? 0);
        $rate = $item->rate ?? 0;

        return RequestResponse::ok('success', ['amount' => $amount, 'rate' => $rate]);
    }

    public function fetchEmployeesForSettings(Request $request)
    {
        $business = Business::findBySlug(session('active_business_slug'));
        if (!$business) return RequestResponse::badRequest('Business not found.');

        $year = $request->year;
        $month = $request->month;

        $employees = $this->getFilteredEmployees($request, $business);
        $employees->load([
            'user',
            'employeeAllowances.allowance',
            'employeeDeductions.deduction',
            'reliefs' => fn($q) => $q->whereNotNull('relief_id'),
            'overtimes' => fn($q) => $q->whereYear('date', $year)->whereMonth('date', $month),
            'loans.repayments',
            'advances',
        ]);

        $payrollSettings = PayrollSettings::where('year', $year)
            ->where('month', $month)
            ->whereIn('employee_id', $employees->pluck('id'))
            ->get()
            ->keyBy('employee_id');

        $formattedEmployees = $employees->map(function ($employee) use ($payrollSettings, $year, $month) {
            $settings = $payrollSettings->get($employee->id);
            $hasSettings = !is_null($settings);

            $mapItems = function ($type, $settingsData, $employeeData, $modelClass, $key) use ($employee, $hasSettings) {
                $sourceData = $employeeData->mapWithKeys(function ($item) use ($modelClass, $employee, $key, $type) {
                    if ($type === 'reliefs') {
                        $itemId = $item->id;
                        $pivotAmount = $item->pivot->amount ?? null;
                        $pivotRate = $item->pivot->rate ?? null;
                        $pivotIsActive = $item->pivot->is_active ?? true;
                    } else {
                        $itemId = $item->$key;
                        $pivotAmount = $item->amount ?? null;
                        $pivotRate = $item->rate ?? null;
                        $pivotIsActive = $item->is_active ?? true;
                    }

                    $defaultItem = $modelClass::find($itemId);
                    $itemName = $defaultItem ? $defaultItem->name : "Unknown " . ucfirst(substr($type, 0, -1)) . " (ID: {$itemId})";

                    return [$itemId => [
                        'user_id' => $employee->user_id,
                        'employee_code' => $employee->employee_code,
                        'name' => $employee->user?->name ?? 'N/A',
                        'item_name' => $itemName,
                        'item_id' => $itemId,
                        'amount' => floatval($pivotAmount ?? $defaultItem?->amount ?? 0),
                        'rate' => floatval($pivotRate ?? $defaultItem?->rate ?? 0),
                        'is_active' => $pivotIsActive,
                    ]];
                })->all();

                return $hasSettings && $settingsData ? array_merge($sourceData, array_map(function ($item) {
                    return array_merge($item, [
                        'amount' => floatval($item['amount'] ?? 0),
                        'rate' => floatval($item['rate'] ?? 0),
                    ]);
                }, $settingsData)) : $sourceData;
            };

            $mapOvertime = function ($settingsData, $employeeOvertimes) use ($employee, $hasSettings) {
                $sourceData = $employeeOvertimes->mapWithKeys(function ($overtime) use ($employee) {
                    return [$overtime->id => [
                        'user_id' => $employee->user_id,
                        'employee_code' => $employee->employee_code,
                        'name' => $employee->user?->name ?? 'N/A',
                        'item_name' => "Overtime on {$overtime->date?->format('Y-m-d')}",
                        'item_id' => $overtime->id,
                        'amount' => floatval($overtime->overtime_hours ?? 0),
                        'is_active' => $overtime->to_be_paid ?? false,
                    ]];
                })->all();

                return $hasSettings && $settingsData ? array_merge($sourceData, array_map(function ($item) {
                    return array_merge($item, ['amount' => floatval($item['amount'] ?? 0)]);
                }, $settingsData)) : $sourceData;
            };

            $mapLoansAdvances = function ($settingsData, $employeeData, $type) use ($employee, $hasSettings) {
                $sourceData = $employeeData->mapWithKeys(function ($item) use ($employee, $type) {
                    $remaining = $type === 'loans' ? floatval($item->amount - ($item->repayments->sum('amount') ?? 0)) : floatval($item->amount);
                    return [$item->id => [
                        'user_id' => $employee->user_id,
                        'employee_code' => $employee->employee_code,
                        'name' => $employee->user?->name ?? 'N/A',
                        'item_name' => $type === 'loans' ? "Loan started {$item->start_date?->format('Y-m-d')}" : "Advance on {$item->date?->format('Y-m-d')}",
                        'item_id' => $item->id,
                        'amount' => $remaining > 0 ? $remaining : 0,
                        'is_active' => $item->is_active ?? ($remaining > 0),
                    ]];
                })->all();

                return $hasSettings && $settingsData ? array_merge($sourceData, array_map(function ($item) {
                    return array_merge($item, ['amount' => floatval($item['amount'] ?? 0)]);
                }, $settingsData)) : $sourceData;
            };

            $formattedEmployee = [
                'id' => $employee->id,
                'name' => $employee->user?->name ?? 'N/A',
                'employee_code' => $employee->employee_code,
                'allowances' => $mapItems('allowances', $settings?->allowances, $employee->employeeAllowances, Allowance::class, 'allowance_id'),
                'deductions' => $mapItems('deductions', $settings?->deductions, $employee->employeeDeductions, Deduction::class, 'deduction_id'),
                'reliefs' => $mapItems('reliefs', $settings?->reliefs, $employee->reliefs, Relief::class, 'relief_id'),
                'overtimes' => $mapOvertime($settings?->overtime, $employee->overtimes),
                'loans' => $mapLoansAdvances($settings?->loans, $employee->loans, 'loans'),
                'advances' => $mapLoansAdvances($settings?->advances, $employee->advances, 'advances'),
                'absenteeism_charge' => [
                    'user_id' => $employee->user_id,
                    'employee_code' => $employee->employee_code,
                    'name' => $employee->user?->name ?? 'N/A',
                    'item_name' => 'Absenteeism Charge',
                    'item_id' => null,
                    'amount' => floatval($hasSettings && !is_null($settings->absenteeism_charge) ? $settings->absenteeism_charge : 0),
                ],
            ];

            Log::info("Formatted employee data for ID {$employee->id}", $formattedEmployee);

            return $formattedEmployee;
        })->values()->toArray();

        return RequestResponse::ok('success', ['employees' => $formattedEmployees]);
    }

    public function saveSettings(Request $request)
    {
        $business = Business::findBySlug(session('active_business_slug'));
        if (!$business) return RequestResponse::badRequest('Business not found.');

        $year = $request->year;
        $month = $request->month;
        $employees = $request->employees;

        Log::info('Incoming payroll settings data:', [
            'year' => $year,
            'month' => $month,
            'employees' => $employees,
        ]);

        if (!is_numeric($year) || !is_numeric($month) || !is_array($employees) || empty($employees)) {
            return RequestResponse::badRequest('Missing or invalid required fields: year, month, or employees.');
        }

        try {
            DB::beginTransaction();
            foreach ($employees as $employeeData) {
                if (!isset($employeeData['id']) || !is_numeric($employeeData['id'])) {
                    throw new \Exception("Invalid or missing employee_id in data: " . json_encode($employeeData));
                }

                $employeeId = $employeeData['id'];
                $employee = Employee::with('user')->findOrFail($employeeId);
                $existingSettings = PayrollSettings::where('employee_id', $employeeId)
                    ->where('year', $year)
                    ->where('month', $month)
                    ->first();

                $mergedData = [
                    'allowances' => $this->formatSettingsData($employeeData['allowances'] ?? [], $existingSettings?->allowances ?? [], $employee, Allowance::class),
                    'deductions' => $this->formatSettingsData($employeeData['deductions'] ?? [], $existingSettings?->deductions ?? [], $employee, Deduction::class),
                    'reliefs' => $this->formatSettingsData($employeeData['reliefs'] ?? [], $existingSettings?->reliefs ?? [], $employee, Relief::class),
                    'overtime' => $this->formatSettingsData($employeeData['overtime'] ?? [], $existingSettings?->overtime ?? [], $employee, Overtime::class),
                    'loans' => $this->formatSettingsData($employeeData['loans'] ?? [], $existingSettings?->loans ?? [], $employee, Loan::class),
                    'advances' => $this->formatSettingsData($employeeData['advances'] ?? [], $existingSettings?->advances ?? [], $employee, Advance::class),
                    'absenteeism_charge' => floatval($employeeData['absenteeism_charge']['amount'] ?? ($existingSettings?->absenteeism_charge ?? 0)),
                ];

                Log::info("Saving payroll settings for employee ID {$employeeId}", $mergedData);

                PayrollSettings::updateOrCreate(
                    [
                        'employee_id' => $employeeId,
                        'year' => $year,
                        'month' => $month,
                    ],
                    $mergedData
                );
            }
            DB::commit();
            return RequestResponse::ok('success', 'Payroll settings saved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to save payroll settings: ' . $e->getMessage());
            return RequestResponse::badRequest('Failed to save payroll settings: ' . $e->getMessage());
        }
    }

    private function formatSettingsData($newData, $existingData, $employee, $modelClass)
    {
        $formatted = [];

        $existingData = is_array($existingData) ? $existingData : [];
        $existingById = array_map(function ($item) {
            return [
                'amount' => floatval($item['amount'] ?? 0),
                'rate' => floatval($item['rate'] ?? 0),
                'is_active' => filter_var($item['is_active'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'item_name' => $item['item_name'] ?? null,
                'user_id' => $item['user_id'] ?? null,
                'employee_code' => $item['employee_code'] ?? null,
                'name' => $item['name'] ?? null,
                'item_id' => $item['item_id'] ?? null,
            ];
        }, array_column($existingData, null, 'item_id'));

        // Process all new data (active and inactive items)
        foreach ($newData as $id => $item) {
            $defaultItem = $modelClass::find($id);
            $itemName = $defaultItem ? $defaultItem->name : ($item['item_name'] ?? "Unknown Item (ID: {$id})");

            if ($modelClass === Overtime::class) {
                $itemName = $defaultItem ? "Overtime on {$defaultItem->date?->format('Y-m-d')}" : $itemName;
            } elseif ($modelClass === Loan::class) {
                $itemName = $defaultItem ? "Loan started {$defaultItem->start_date?->format('Y-m-d')}" : $itemName;
            } elseif ($modelClass === Advance::class) {
                $itemName = $defaultItem ? "Advance on {$defaultItem->date?->format('Y-m-d')}" : $itemName;
            }

            $newItem = [
                'user_id' => $employee->user_id,
                'employee_code' => $employee->employee_code,
                'name' => $employee->user?->name ?? 'N/A',
                'item_name' => $itemName,
                'item_id' => $id,
                'amount' => floatval($item['amount'] ?? 0),
                'rate' => floatval($item['rate'] ?? 0),
                'is_active' => filter_var($item['is_active'] ?? false, FILTER_VALIDATE_BOOLEAN),
            ];

            $formatted[$id] = $newItem;
        }

        foreach ($existingById as $id => $existingItem) {
            if (!isset($newData[$id])) {
                $formatted[$id] = array_merge($existingItem, [
                    'is_active' => false,
                    'amount' => floatval($existingItem['amount'] ?? 0),
                    'rate' => floatval($existingItem['rate'] ?? 0),
                ]);
            }
        }

        return $formatted;
    }

    public function store(Request $request)
    {
        $business = Business::findBySlug(session('active_business_slug'));
        if (!$business) {
            return RequestResponse::badRequest('Business not found.');
        }

        // Retrieve previewed data from session
        $previewData = session('payroll_preview_data');
        if (!$previewData) {
            return RequestResponse::badRequest('No preview data found. Please generate a preview first.');
        }

        $payrollData = $previewData['payroll_data'];
        $year = $previewData['year'];
        $month = $previewData['month'];
        $businessId = $previewData['business_id'];
        $locationId = $previewData['location_id'];
        $options = $previewData['options'];
        $nonExemptedEmployeeIds = $previewData['non_exempted_employee_ids'];

        Log::info('Store method started', [
            'business_id' => $businessId,
            'year' => $year,
            'month' => $month,
            'employee_count' => count($nonExemptedEmployeeIds),
            'request_data' => $request->all(),
        ]);

        if (empty($payrollData)) {
            Log::warning('No payroll data in session');
            return RequestResponse::badRequest('No payroll data available to store.');
        }

        return $this->handleTransaction(function () use ($business, $payrollData, $year, $month, $businessId, $locationId, $options) {
            $payroll = Payroll::create([
                'payrun_year' => $year,
                'payrun_month' => $month,
                'business_id' => $businessId,
                'location_id' => $locationId,
                'payroll_type' => 'monthly',
                'status' => 'open',
                'staff' => count($payrollData),
                'currency' => $business->currency ?? 'KES',
            ]);

            Log::info('Payroll created', ['payroll_id' => $payroll->id]);

            foreach ($payrollData as $data) {
                $paymentDetail = EmployeePaymentDetail::where('employee_id', $data['employee_id'])->first();
                EmployeePayroll::create([
                    'payroll_id' => $payroll->id,
                    'employee_id' => $data['employee_id'],
                    'employee_payment_detail_id' => $paymentDetail ? $paymentDetail->id : null,
                    'basic_salary' => $data['basic_salary'],
                    'gross_pay' => $data['gross_pay'],
                    'overtime' => json_encode(['amount' => $data['overtime']]),
                    'allowances' => json_encode($data['allowances']),
                    'shif' => $data['shif'],
                    'nssf' => $data['nssf'],
                    'paye' => $data['paye'],
                    'paye_before_reliefs' => $data['paye_before_reliefs'],
                    'housing_levy' => $data['housing_levy'],
                    'helb' => $data['helb'],
                    'taxable_income' => $data['taxable_income'],
                    'personal_relief' => $data['personal_relief'],
                    'insurance_relief' => $data['insurance_relief'],
                    'pay_after_tax' => $data['gross_pay'] - $data['paye'],
                    'loan_repayment' => $data['loan_repayment'],
                    'advance_recovery' => $data['advance_recovery'],
                    'deductions_after_tax' => $data['gross_pay'] - $data['paye'] - $data['net_pay'],
                    'net_pay' => $data['net_pay'],
                    'deductions' => json_encode($data['deductions']),
                    'bank_name' => $data['bank_name'],
                    'account_number' => $data['account_number'],
                    'attendance_present' => $data['attendance_present'],
                    'attendance_absent' => $data['attendance_absent'],
                    'days_in_month' => $data['days_in_month'],
                ]);

                $this->updateLoanAndAdvance($data, $year, $month, $options);

                Log::info('Employee payroll stored', ['employee_id' => $data['employee_id']]);
            }

            // Clear session data after successful storage
            session()->forget('payroll_preview_data');

            return RequestResponse::ok('success', [
                'redirect_url' => route('business.payroll.view', ['business' => $business->slug, 'id' => $payroll->id]),
            ]);
        }, function ($e) use ($year, $month) {
            Log::error('Payroll store failed', [
                'year' => $year,
                'month' => $month,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
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
                    $amount = $allowance->amount;
                    EmployeeAllowance::updateOrCreate(
                        ['employee_id' => $employee->id, 'allowance_id' => $allowanceId],
                        ['amount' => $amount, 'is_active' => true]
                    );
                }
            }
        }

        if ($request->deductions) {
            foreach ($request->deductions as $deductionId) {
                $deduction = Deduction::find($deductionId);
                if ($deduction) {
                    $amount = $deduction->amount;
                    EmployeeDeduction::updateOrCreate(
                        ['employee_id' => $employee->id, 'deduction_id' => $deductionId],
                        ['amount' => $amount, 'is_active' => true]
                    );
                }
            }
        }

        if ($request->reliefs) {
            foreach ($request->reliefs as $reliefId) {
                $relief = Relief::find($reliefId);
                if ($relief) {
                    $amount = $relief->amount;
                    EmployeeRelief::updateOrCreate(
                        ['employee_id' => $employee->id, 'relief_id' => $reliefId],
                        ['amount' => $amount, 'is_active' => true]
                    );
                }
            }
        }

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
                'location',
                'employmentDetails.department',
                'employmentDetails.jobCategory',
                'employeeAllowances.allowance',
                'employeeDeductions.deduction',
                'reliefs' => fn($q) => $q->withPivot('amount', 'is_active', 'start_date', 'end_date'),
                'overtimes' => fn($q) => $q->whereYear('date', $request->year)->whereMonth('date', $request->month),
                'advances' => fn($q) => $q->whereYear('date', $request->year)->whereMonth('date', $request->month),
                'loans.repayments' => fn($q) => $q->where('start_date', '<=', Carbon::create($request->year, $request->month)->endOfMonth())
                    ->where('end_date', '>=', Carbon::create($request->year, $request->month)->startOfMonth()),
                'attendances' => fn($q) => $q->whereYear('date', $request->year)->whereMonth('date', $request->month),
            ]);

        if ($request->location_id) {
            if (str_starts_with($request->location_id, 'business_')) {
                $query->whereNull('location_id');
            } else {
                $query->where('location_id', $request->location_id);
            }
        }
        if ($request->department_id) {
            $query->whereHas('employmentDetails', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
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
            $employeeWarnings = [];

            if (!$employee->paymentDetails) {
                $employeeWarnings[] = 'Missing payment details';
            }

            if ($employee->paymentDetails && floatval($employee->paymentDetails->basic_salary) == 0) {
                $employeeWarnings[] = 'Basic salary is 0';
            }

            if (!$employee->tax_no) {
                $employeeWarnings[] = 'Missing KRA PIN';
            }

            if (!$employee->user || !$employee->user->email) {
                $employeeWarnings[] = 'Missing email';
            }

            if (!empty($employeeWarnings)) {
                $warnings[$employee->id] = [
                    'name' => $employee->user?->name ?? 'Unknown',
                    'employee_code' => $employee->employee_code ?? 'N/A',
                    'messages' => $employeeWarnings,
                ];
            }
        }
        return $warnings;
    }

    protected function parseOptions(Request $request)
    {
        $exempted = $request->input('exempted_employees', []);
        if (is_string($exempted)) {
            $exempted = json_decode($exempted, true) ?? [];
        }

        if (!is_array($exempted)) {
            $exempted = [];
        }

        $options = [
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

        foreach (['recover_advances', 'recover_loans', 'pay_overtime'] as $key) {
            if (!isset($options[$key]['apply']) || !is_string($options[$key]['apply'])) {
                $options[$key]['apply'] = 'none';
            }
            if (!isset($options[$key]['specific']) || !is_array($options[$key]['specific'])) {
                $options[$key]['specific'] = [];
            }
        }

        return $options;
    }

    public function preview(Request $request)
    {
        $business = Business::findBySlug(session('active_business_slug'));
        if (!$business) {
            return RequestResponse::badRequest('Business not found.');
        }

        $year = $request->year;
        $month = $request->month;

        $employees = $this->getFilteredEmployees($request, $business);
        $options = $this->parseOptions($request);

        $nonExemptedEmployees = $employees->filter(function ($e) use ($options) {
            return !isset($options['exempted_employees'][$e->id]) || $options['exempted_employees'][$e->id] != 1;
        });

        $warnings = $this->checkMissingData($nonExemptedEmployees);

        Log::info('Preview request data', [
            'request' => $request->all(),
            'options' => $options,
            'non_exempted_employee_ids' => $nonExemptedEmployees->pluck('id')->toArray(),
        ]);

        if (!empty($warnings)) {
            return response()->json([
                'message' => 'Resolve warnings before previewing.',
                'type' => 'warnings',
                'warnings' => $warnings
            ], 400);
        }

        // Load payroll settings for non-exempted employees
        $payrollSettings = PayrollSettings::where('year', $year)
            ->where('month', $month)
            ->whereIn('employee_id', $nonExemptedEmployees->pluck('id'))
            ->get()
            ->keyBy('employee_id');

        $options['payroll_settings'] = $payrollSettings->mapWithKeys(function ($setting) {
            return [
                $setting->employee_id => [
                    'allowances' => $setting->allowances ?? [],
                    'deductions' => $setting->deductions ?? [],
                    'reliefs' => $setting->reliefs ?? [],
                    'overtime' => $setting->overtime ?? [],
                    'loans' => $setting->loans ?? [],
                    'advances' => $setting->advances ?? [],
                    'absenteeism_charge' => $setting->absenteeism_charge ?? 0,
                ],
            ];
        })->toArray();

        $payrollData = $this->calculatePayroll($nonExemptedEmployees, $year, $month, $options);

        Log::debug('Payroll data calculated', ['payroll_data' => $payrollData]);

        // Store payroll data and metadata in session
        session([
            'payroll_preview_data' => [
                'payroll_data' => $payrollData,
                'year' => $year,
                'month' => $month,
                'business_id' => $business->id,
                'location_id' => str_starts_with($request->location_id, 'business_') ? null : $request->location_id,
                'options' => $options,
                'non_exempted_employee_ids' => $nonExemptedEmployees->pluck('id')->toArray(),
            ]
        ]);

        return RequestResponse::ok('success', [
            'html' => view('payroll._preview', ['payrollData' => array_values($payrollData), 'options' => $options])->render(),
            'options' => $options,
        ]);
    }

    protected function calculatePayroll($employees, $year, $month, $options)
    {
        $business = Business::findBySlug(session('active_business_slug'));
        $payrollData = [];
        $period = Carbon::create($year, $month);
        $daysInMonth = $period->daysInMonth;

        Log::info('Starting payroll calculation', [
            'year' => $year,
            'month' => $month,
            'employee_count' => $employees->count(),
        ]);

        foreach ($employees as $employee) {
            $employeeId = $employee->id;
            $settings = $options['payroll_settings'][$employeeId] ?? null;

            Log::debug('Processing employee', ['employee_id' => $employeeId]);

            // Fetch base salary and bank details from employee_payment_details
            $paymentDetail = EmployeePaymentDetail::where('employee_id', $employeeId)->first();
            $basicSalary = floatval($paymentDetail->basic_salary ?? 0);
            $bankName = $paymentDetail->bank_name ?? 'N/A';
            $accountNumber = $paymentDetail->account_number ?? 'N/A';

            // Prorate basic salary based on attendance
            // $presentDays = $employee->attendances->where('is_absent', false)->count();
            $presentDays = 31;
            $absentDays = $daysInMonth - $presentDays;
            $proratedBasicSalary = $basicSalary * ($presentDays / $daysInMonth);

            // Get payroll items
            $allowances = $this->getEmployeeItems($employee, 'allowances', $settings, Allowance::class, EmployeeAllowance::class, $proratedBasicSalary);
            $totalTaxableAllowances = array_sum(array_map(fn($a) => $a['is_taxable'] ? $a['amount'] : 0, $allowances));
            $totalNonTaxableAllowances = array_sum(array_map(fn($a) => !$a['is_taxable'] ? $a['amount'] : 0, $allowances));

            $overtimePay = $this->calculateOvertime($employeeId, $year, $month, $settings, $options, $proratedBasicSalary);

            // Gross pay
            $grossPay = $proratedBasicSalary + $totalTaxableAllowances + $totalNonTaxableAllowances + $overtimePay;

            // Statutory Deductions
            $nssf = $this->calculateNSSF($grossPay);
            $shif = $this->calculateStatutoryDeduction($business->id, 'SHIF', $grossPay);
            $housingLevy = $grossPay * 0.015;
            $helb = $this->calculateHelb($employeeId, $grossPay, $business->id);

            // Reliefs (insurance-related fields not in employee_payment_details, default to 0 unless elsewhere)
            $reliefs = $this->getEmployeeItems($employee, 'reliefs', $settings, Relief::class, EmployeeRelief::class, $grossPay);
            $personalRelief = min(2400, array_key_exists('Personal Relief', $reliefs) ? $reliefs['Personal Relief']['amount'] : 0);
            $insuranceRelief = 0; // No insurance fields in employee_payment_details; adjust if elsewhere
            $totalReliefs = $personalRelief + $insuranceRelief;

            // Taxable Income and PAYE
            $taxableIncome = max(0, $grossPay - $nssf);
            $payeBeforeReliefs = $this->calculatePAYE($taxableIncome);
            $paye = max(0, $payeBeforeReliefs - $totalReliefs);

            // Custom Deductions
            $deductions = $this->getEmployeeItems($employee, 'deductions', $settings, Deduction::class, EmployeeDeduction::class, $grossPay);
            $totalCustomDeductions = array_sum(array_map(fn($d) => $d['amount'], $deductions));

            // Loans and Advances
            $loanRepayment = $this->calculateLoanRepayment($employeeId, $year, $month, $settings, $options);
            $advanceRecovery = $this->calculateAdvanceRecovery($employeeId, $year, $month, $settings, $options);

            // Absenteeism Charge
            $absenteeismCharge = floatval($settings['absenteeism_charge'] ?? 0);

            // Total Deductions
            $totalDeductions = $paye + $nssf + $shif + $housingLevy + $helb + $totalCustomDeductions + $loanRepayment + $advanceRecovery + $absenteeismCharge;

            // Net Pay
            $netPay = $grossPay - $totalDeductions;

            $payrollData[$employeeId] = [
                'employee_id' => $employeeId,
                'employee' => $employee,
                'basic_salary' => $proratedBasicSalary,
                'gross_pay' => $grossPay,
                'overtime' => $overtimePay,
                'allowances' => $allowances,
                'shif' => $shif,
                'nssf' => $nssf,
                'paye' => $paye,
                'paye_before_reliefs' => $payeBeforeReliefs,
                'housing_levy' => $housingLevy,
                'helb' => $helb,
                'loan_repayment' => $loanRepayment,
                'advance_recovery' => $advanceRecovery,
                'deductions' => array_merge($deductions, [['name' => 'Absenteeism Charge', 'amount' => $absenteeismCharge]]),
                'net_pay' => $netPay,
                'taxable_income' => $taxableIncome,
                'reliefs' => array_merge($reliefs, [
                    'Personal Relief' => ['name' => 'Personal Relief', 'amount' => $personalRelief, 'tax_application' => 'before_tax'],
                    'Insurance Relief' => ['name' => 'Insurance Relief', 'amount' => $insuranceRelief, 'tax_application' => 'before_tax'],
                ]),
                'personal_relief' => $personalRelief,
                'insurance_relief' => $insuranceRelief,
                'bank_name' => $bankName,
                'account_number' => $accountNumber,
                'currency' => $paymentDetail->currency ?? 'KES',
                'payment_mode' => $paymentDetail->payment_mode ?? 'N/A',
                'attendance_present' => $presentDays,
                'attendance_absent' => $absentDays,
                'days_in_month' => $daysInMonth,
            ];

            Log::debug('Employee payroll data', [
                'employee_id' => $employeeId,
                'data' => $payrollData[$employeeId],
                'basic_salary' => $proratedBasicSalary,
                'bank_name' => $bankName,
                'account_number' => $accountNumber,
            ]);
        }

        Log::info('Payroll calculation completed', ['payroll_data_count' => count($payrollData)]);
        return $payrollData;
    }

    protected function calculateStatutoryDeduction($businessId, $name, $amount, $isRelief = false)
    {
        $formula = PayrollFormula::where('business_id', $businessId)->where('name', $name)->first();
        if (!$formula) {
            if ($isRelief) return 2400; // Default personal relief
            switch ($name) {
                case 'SHIF':
                    return $amount * 0.0275; // 2.75%
                case 'NHDF':
                    return $amount * 0.015; // 1.5%
                case 'NSSF':
                    return min(1080, $amount >= 18000 ? 1080 : 200); // Simplified tier
                default:
                    return 0;
            }
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

    protected function calculateOvertime($employeeId, $year, $month, $settings, $options, $basicSalary)
    {
        $hourlyRate = $basicSalary / 173; // Standard 173 hours/month
        $totalOvertimePay = 0;

        if (!is_null($settings) && !empty($settings['overtime'])) {
            foreach ($settings['overtime'] as $item) {
                if ($item['is_active']) {
                    $overtime = Overtime::find($item['item_id']);
                    if ($overtime) {
                        $hours = floatval($item['amount']);
                        $rate = $overtime->rate ?? 1.5; // Default 1.5x rate
                        $totalOvertimePay += $hours * $hourlyRate * $rate;
                    }
                }
            }
        } elseif ($options['pay_overtime']['apply'] !== 'none') {
            $overtimes = Overtime::where('employee_id', $employeeId)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->get();
            foreach ($overtimes as $overtime) {
                if ($overtime->to_be_paid || isset($options['pay_overtime']['specific'][$overtime->id])) {
                    $hours = $overtime->overtime_hours;
                    $rate = $overtime->rate ?? 1.5;
                    $totalOvertimePay += $hours * $hourlyRate * $rate;
                }
            }
        }

        return $totalOvertimePay;
    }

    protected function calculateAdvanceRecovery($employeeId, $year, $month, $settings, $options)
    {
        $totalRecovery = 0;

        if (!is_null($settings) && !empty($settings['advances'])) {
            foreach ($settings['advances'] as $item) {
                if ($item['is_active']) {
                    $advance = Advance::find($item['item_id']);
                    if ($advance) {
                        $totalRecovery += min(floatval($item['amount']), floatval($advance->amount));
                    }
                }
            }
        } elseif ($options['recover_advances']['apply'] !== 'none') {
            $advances = Advance::where('employee_id', $employeeId)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->get();
            foreach ($advances as $advance) {
                if (isset($options['recover_advances']['specific'][$advance->id])) {
                    $totalRecovery += min($options['recover_advances']['specific'][$advance->id], floatval($advance->amount));
                }
            }
        }

        return $totalRecovery;
    }

    protected function calculateLoanRepayment($employeeId, $year, $month, $settings, $options)
    {
        $totalRepayment = 0;

        if (!is_null($settings) && !empty($settings['loans'])) {
            foreach ($settings['loans'] as $item) {
                if ($item['is_active']) {
                    $loan = Loan::find($item['item_id']);
                    if ($loan) {
                        $repaid = $loan->repayments->sum('amount');
                        $remaining = max(0, $loan->amount - $repaid);
                        $totalRepayment += min(floatval($item['amount']), $remaining);
                    }
                }
            }
        } elseif ($options['recover_loans']['apply'] !== 'none') {
            $loans = Loan::where('employee_id', $employeeId)
                ->where('start_date', '<=', Carbon::create($year, $month)->endOfMonth())
                ->get();
            foreach ($loans as $loan) {
                $repaid = $loan->repayments->sum('amount');
                $remaining = max(0, $loan->amount - $repaid);
                if ($remaining > 0 && isset($options['recover_loans']['specific'][$loan->id])) {
                    $totalRepayment += min($options['recover_loans']['specific'][$loan->id], $remaining);
                }
            }
        }

        return $totalRepayment;
    }

    protected function calculateNSSF($grossPay)
    {
        return min($grossPay * 0.06, 1080); // 6% capped at 1,080 KES (2025 standard)
    }

    protected function calculateNHIF($grossPay)
    {
        // Kenyan NHIF bands as of 2025 (example rates)
        if ($grossPay <= 5999) return 150;
        if ($grossPay <= 7999) return 300;
        if ($grossPay <= 11999) return 400;
        if ($grossPay <= 14999) return 500;
        if ($grossPay <= 19999) return 600;
        if ($grossPay <= 24999) return 750;
        if ($grossPay <= 29999) return 850;
        if ($grossPay <= 34999) return 900;
        if ($grossPay <= 39999) return 950;
        if ($grossPay <= 44999) return 1000;
        if ($grossPay <= 49999) return 1100;
        if ($grossPay <= 59999) return 1200;
        if ($grossPay <= 69999) return 1300;
        if ($grossPay <= 79999) return 1400;
        if ($grossPay <= 89999) return 1500;
        if ($grossPay <= 99999) return 1600;
        return 1700; // Above 100,000
    }

    protected function calculatePAYE($taxableIncome)
    {
        // Kenyan PAYE rates as of 2025 (simplified)
        $tax = 0;
        if ($taxableIncome <= 24000) {
            $tax = $taxableIncome * 0.10;
        } elseif ($taxableIncome <= 32333) {
            $tax = 2400 + ($taxableIncome - 24000) * 0.25;
        } else {
            $tax = 4483.25 + ($taxableIncome - 32333) * 0.30;
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

    protected function getEmployeeItems($employee, $type, $settings, $modelClass, $pivotClass, $baseAmount)
    {
        $items = [];
        $hasSettings = !is_null($settings);

        if ($hasSettings && !empty($settings[$type])) {
            foreach ($settings[$type] as $item) {
                if ($item['is_active']) {
                    $modelItem = $modelClass::find($item['item_id']);
                    $isTaxable = $modelItem ? ($modelItem->type !== 'non_taxable') : true;
                    $amount = floatval($item['amount']);
                    if ($modelItem && $modelItem->calculation_basis === 'percentage') {
                        $amount = $baseAmount * ($item['rate'] / 100);
                    }
                    $items[$item['item_name']] = [
                        'name' => $item['item_name'],
                        'amount' => $amount,
                        'is_taxable' => $isTaxable,
                        'tax_application' => $modelItem->tax_application ?? 'before_tax',
                    ];
                }
            }
        } else {
            $relation = ($type === 'allowances') ? 'employeeAllowances' : (($type === 'deductions') ? 'employeeDeductions' : 'reliefs');
            $pivotItems = $employee->$relation;
            foreach ($pivotItems as $pivotItem) {
                $itemId = $pivotItem->{"{$type}_id"} ?? $pivotItem->id;
                $modelItem = $modelClass::find($itemId);
                if ($modelItem && $pivotItem->is_active) {
                    $amount = floatval($pivotItem->amount ?? $modelItem->amount ?? 0);
                    if ($modelItem->calculation_basis === 'percentage') {
                        $amount = $baseAmount * ($pivotItem->rate ?? $modelItem->rate ?? 0) / 100;
                    }
                    $isTaxable = $modelItem->type !== 'non_taxable';
                    $items[$modelItem->name] = [
                        'name' => $modelItem->name,
                        'amount' => $amount,
                        'is_taxable' => $isTaxable,
                        'tax_application' => $modelItem->tax_application ?? 'before_tax',
                    ];
                }
            }
        }

        return $items;
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

            // Calculate updated totals after deletion
            $payrolls = Payroll::where('business_id', $business->id)
                ->with('employeePayrolls')
                ->get();
            $totalPayroll = $payrolls->sum(fn($p) => $p->employeePayrolls->sum('net_pay'));

            return RequestResponse::ok('Payroll deleted successfully.', [
                'payroll_count' => $payrolls->count(),
                'total_payroll' => number_format($totalPayroll, 2),
                'total_net_pay' => number_format($totalPayroll, 2),
            ]);
        });
    }

    public function closeMonth(Request $request, $id)
    {
        return $this->handleTransaction(function () use ($id) {
            $business = Business::findBySlug(session('active_business_slug'));
            if (!$business) {
                return RequestResponse::badRequest('Business not found.');
            }

            $payroll = Payroll::where('business_id', $business->id)->where('id', $id)->firstOrFail();
            $newStatus = $payroll->status === 'closed' ? 'open' : 'closed';
            $payroll->update(['status' => $newStatus]);

            return RequestResponse::ok("Payroll month " . ($newStatus === 'closed' ? 'closed' : 'opened') . " successfully.", [
                'status' => $newStatus,
            ]);
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
        $businessSlug = $request->route('business') ?? session('active_business_slug');
        $business = Business::findBySlug($businessSlug);
        if (!$business) {
            Log::error("Business not found for slug: " . ($businessSlug ?? 'Not set'));
            return response()->json(['error' => 'Business not found.'], 400);
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

        // Prepare comprehensive payroll data
        $data = $payroll->employeePayrolls->map(function ($ep) {
            $deductions = json_decode($ep->deductions, true) ?? [];
            $overtime = json_decode($ep->overtime, true) ?? ['amount' => 0];
            $allowances = json_decode($ep->allowances, true) ?? [];
            $customDeductions = array_filter($deductions, fn($d) => !in_array($d['name'] ?? '', [
                'SHIF',
                'NSSF',
                'PAYE',
                'Housing Levy',
                'HELB',
                'Loan Repayment',
                'Advance Recovery',
                'Absenteeism Charge'
            ]));
            $totalCustomDeductions = array_sum(array_map(fn($d) => $d['amount'] ?? 0, $customDeductions));

            return [
                'employee_name' => $ep->employee->user->name ?? 'N/A',
                'employee_code' => $ep->employee->employee_code ?? 'N/A',
                'tax_no' => $ep->employee->tax_no ?? 'N/A',
                'basic_salary' => (float) ($ep->basic_salary ?? 0),
                'gross_pay' => (float) ($ep->gross_pay ?? 0),
                'overtime' => (float) ($overtime['amount'] ?? 0),
                'shif' => (float) ($ep->shif ?? ($deductions['shif'] ?? 0)),
                'nssf' => (float) ($ep->nssf ?? ($deductions['nssf'] ?? 0)),
                'paye' => (float) ($ep->paye ?? ($deductions['paye'] ?? 0)),
                'paye_before_reliefs' => (float) ($ep->paye_before_reliefs ?? 0),
                'housing_levy' => (float) ($ep->housing_levy ?? ($deductions['housing_levy'] ?? 0)),
                'helb' => (float) ($ep->helb ?? ($deductions['helb'] ?? 0)),
                'taxable_income' => (float) ($ep->taxable_income ?? 0),
                'personal_relief' => (float) ($ep->personal_relief ?? 0),
                'insurance_relief' => (float) ($ep->insurance_relief ?? 0),
                'pay_after_tax' => (float) ($ep->pay_after_tax ?? 0),
                'loan_repayment' => (float) ($ep->loan_repayment ?? ($deductions['loan_repayment'] ?? 0)),
                'advance_recovery' => (float) ($ep->advance_recovery ?? ($deductions['advance_recovery'] ?? 0)),
                'custom_deductions' => (float) $totalCustomDeductions,
                'deductions_after_tax' => (float) ($ep->deductions_after_tax ?? 0),
                'net_pay' => (float) ($ep->net_pay ?? 0),
                'attendance_present' => (int) ($ep->attendance_present ?? 0),
                'attendance_absent' => (int) ($ep->attendance_absent ?? 0),
                'days_in_month' => (int) ($ep->days_in_month ?? 0),
                'bank_name' => $ep->bank_name ?? 'N/A',
                'account_number' => $ep->account_number ?? 'N/A',
            ];
        })->toArray();

        // Calculate totals
        $totals = [
            'totalBasicSalary' => array_sum(array_column($data, 'basic_salary')),
            'totalGrossPay' => array_sum(array_column($data, 'gross_pay')),
            'totalOvertime' => array_sum(array_column($data, 'overtime')),
            'totalShif' => array_sum(array_column($data, 'shif')),
            'totalNssf' => array_sum(array_column($data, 'nssf')),
            'totalPaye' => array_sum(array_column($data, 'paye')),
            'totalPayeBeforeReliefs' => array_sum(array_column($data, 'paye_before_reliefs')),
            'totalHousingLevy' => array_sum(array_column($data, 'housing_levy')),
            'totalHelb' => array_sum(array_column($data, 'helb')),
            'totalTaxableIncome' => array_sum(array_column($data, 'taxable_income')),
            'totalPersonalRelief' => array_sum(array_column($data, 'personal_relief')),
            'totalInsuranceRelief' => array_sum(array_column($data, 'insurance_relief')),
            'totalPayAfterTax' => array_sum(array_column($data, 'pay_after_tax')),
            'totalLoans' => array_sum(array_column($data, 'loan_repayment')),
            'totalAdvances' => array_sum(array_column($data, 'advance_recovery')),
            'totalCustomDeductions' => array_sum(array_column($data, 'custom_deductions')),
            'totalDeductionsAfterTax' => array_sum(array_column($data, 'deductions_after_tax')),
            'totalNetPay' => array_sum(array_column($data, 'net_pay')),
            'totalAttendancePresent' => array_sum(array_column($data, 'attendance_present')),
            'totalAttendanceAbsent' => array_sum(array_column($data, 'attendance_absent')),
            'totalDaysInMonth' => array_sum(array_column($data, 'days_in_month')),
        ];
        $totals = array_map('floatval', $totals);

        $fileName = "payroll-{$id}.{$format}";
        $currency = $payroll->currency ?? 'KES';

        switch ($format) {
            case 'pdf':
                try {
                    $pdf = Pdf::loadView('payroll.reports.company_payslip', [
                        'business' => $business,
                        'payroll' => $payroll,
                        'entity' => $entity,
                        'entityType' => $entityType,
                        'data' => $data,
                        'totals' => $totals,
                        'currency' => $currency,
                    ])->setPaper('a4', 'landscape');
                    return $pdf->download($fileName);
                } catch (\Exception $e) {
                    Log::error("PDF generation failed for payroll {$id}: " . $e->getMessage() . "\nStack trace: " . $e->getTraceAsString());
                    return response()->json(['error' => 'Failed to generate PDF: ' . $e->getMessage()], 500);
                }

            case 'csv':
                $headers = array_keys($data[0] ?? []);
                $csvData = implode(',', array_map(fn($key) => ucwords(str_replace('_', ' ', $key)) . ($key === 'bank_name' || $key === 'account_number' || $key === 'employee_name' || $key === 'employee_code' || $key === 'tax_no' ? '' : " ({$currency})"), $headers)) . "\n";
                foreach ($data as $row) {
                    $csvData .= implode(',', array_map(function ($value, $key) {
                        return ($key !== 'bank_name' && $key !== 'account_number' && $key !== 'employee_name' && $key !== 'employee_code' && $key !== 'tax_no' && is_numeric($value))
                            ? number_format($value, 2)
                            : "\"" . str_replace('"', '""', $value) . "\"";
                    }, $row, array_keys($row))) . "\n";
                }
                return Response::make($csvData, 200, [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
                ]);

            case 'xlsx':
                try {
                    return Excel::download(new class($data, $currency) implements
                        \Maatwebsite\Excel\Concerns\FromArray,
                        \Maatwebsite\Excel\Concerns\WithHeadings {
                        private $data;
                        private $currency;

                        public function __construct(array $data, string $currency)
                        {
                            $this->data = $data;
                            $this->currency = $currency;
                        }

                        public function array(): array
                        {
                            return $this->data;
                        }

                        public function headings(): array
                        {
                            return array_map(fn($key) => ucwords(str_replace('_', ' ', $key)) . ($key === 'bank_name' || $key === 'account_number' || $key === 'employee_name' || $key === 'employee_code' || $key === 'tax_no' ? '' : " ({$this->currency})"), array_keys($this->data[0] ?? []));
                        }
                    }, $fileName);
                } catch (\Maatwebsite\Excel\Exceptions\LaravelExcelException $e) {
                    Log::error("Excel generation failed for payroll {$id}: " . $e->getMessage());
                    return response()->json(['error' => 'Failed to generate Excel file.'], 500);
                }

            default:
                Log::warning("Invalid format requested for payroll {$id}: {$format}");
                return response()->json(['error' => 'Invalid format requested.'], 400);
        }
    }

    public function viewPayslip(Request $request, $employeeId)
    {
        $business = Business::findBySlug(session('active_business_slug'));
        if (!$business) {
            return RequestResponse::badRequest('Business not found.');
        }
        $id = $request->employee_id;

        $payrollId = $request->query('payroll_id');
        if (!$payrollId) {
            return RequestResponse::badRequest('Payroll ID is required to view the payslip.');
        }

        $employeePayroll = EmployeePayroll::with([
            'employee.user',
            'payroll.business',
            'payroll.location'
        ])
            ->where('employee_id', $id)
            ->where('payroll_id', $payrollId)
            ->firstOrFail();

        $entity = $business;
        $entityType = 'business';
        if ($employeePayroll->payroll->location_id) {
            $location = Location::where('id', $employeePayroll->payroll->location_id)
                ->where('business_id', $business->id)
                ->first();
            if ($location) {
                $entity = $location;
                $entityType = 'location';
            }
        }

        Log::info($employeePayroll);

        // Currency Conversion Logic
        // $targetCurrency = strtoupper($employeePayroll->employee->user->country ?? 'USD');
        $targetCurrency = 'USD';
        $baseCurrency = $employeePayroll->payroll->currency;

        $exchangeRates = $this->getExchangeRates($baseCurrency, $targetCurrency);

        return view('payroll.reports.payslip', compact('employeePayroll', 'business', 'entity', 'entityType', 'exchangeRates', 'targetCurrency'));
    }

    private function getExchangeRates($baseCurrency, $targetCurrency)
    {
        try {
            // Fetch latest exchange rates
            $response = Http::get("https://api.frankfurter.dev/v1/latest", [
                'base' => $baseCurrency,
                'symbols' => $targetCurrency
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $date = $data['date'] ?? now()->toDateString();
                $exchangeRate = $data['rates'][$targetCurrency] ?? null;

                return [
                    'base' => $baseCurrency,
                    'date' => $date,
                    'rate' => $exchangeRate
                ];
            } else {
                Log::error('Frankfurter API Error: ' . $response->body());
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Frankfurter API Exception: ' . $e->getMessage());
            return null;
        }
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
            ->with([
                'employeePayrolls.employee.user',
                'employeePayrolls.employee.paymentDetails',
                'employeePayrolls.employee.payrollDetail'
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
            'totalBasicSalary' => 0.00,
            'totalGrossPay' => 0.00,
            'totalOvertime' => 0.00,
            'totalShif' => 0.00,
            'totalNssf' => 0.00,
            'totalPaye' => 0.00,
            'totalHousingLevy' => 0.00,
            'totalHelb' => 0.00,
            'totalLoans' => 0.00,
            'totalAdvances' => 0.00,
            'totalCustomDeductions' => 0.00,
            'totalTaxableIncome' => 0.00,
            'totalPersonalRelief' => 0.00,
            'totalInsuranceRelief' => 0.00,
            'totalPayAfterTax' => 0.00,
            'totalDeductionsAfterTax' => 0.00,
            'totalNetPay' => 0.00,
            // Optional fields not currently used in view but kept for consistency
            'totalAbsenteeismCharge' => 0.00,
            'totalMortgageRelief' => 0.00,
            'totalHospRelief' => 0.00,
        ];

        foreach ($payroll->employeePayrolls as $ep) {
            $overtime = json_decode($ep->overtime, true) ?? ['amount' => 0.00];
            $deductions = json_decode($ep->deductions, true) ?? [];

            // Fetch reliefs from payrollDetail if available
            $reliefs = $ep->employee->payrollDetail ? json_decode($ep->employee->payrollDetail->reliefs, true) ?? [] : [];

            $totals['totalBasicSalary'] += (float) ($ep->basic_salary ?? 0);
            $totals['totalGrossPay'] += (float) ($ep->gross_pay ?? 0);
            $totals['totalOvertime'] += (float) ($overtime['amount'] ?? 0);
            $totals['totalShif'] += (float) ($ep->shif ?? ($deductions['shif'] ?? 0));
            $totals['totalNssf'] += (float) ($ep->nssf ?? ($deductions['nssf'] ?? 0));
            $totals['totalPaye'] += (float) ($ep->paye ?? ($deductions['paye'] ?? 0));
            $totals['totalHousingLevy'] += (float) ($ep->housing_levy ?? ($deductions['housing_levy'] ?? 0));
            $totals['totalHelb'] += (float) ($ep->helb ?? ($deductions['helb'] ?? 0));
            $totals['totalLoans'] += (float) ($ep->loan_repayment ?? ($deductions['loan_repayment'] ?? 0));
            $totals['totalAdvances'] += (float) ($ep->advance_recovery ?? ($deductions['advance_recovery'] ?? 0));
            $totals['totalTaxableIncome'] += (float) ($ep->taxable_income ?? 0);
            $totals['totalPersonalRelief'] += (float) ($ep->personal_relief ?? ($reliefs['personal-relief']['amount'] ?? 0));
            $totals['totalInsuranceRelief'] += (float) ($ep->insurance_relief ?? ($reliefs['insurance-relief']['amount'] ?? 0));
            $totals['totalPayAfterTax'] += (float) ($ep->pay_after_tax ?? 0);
            $totals['totalDeductionsAfterTax'] += (float) ($ep->deductions_after_tax ?? 0);
            $totals['totalNetPay'] += (float) ($ep->net_pay ?? 0);

            // Absenteeism Charge
            $absenteeism = array_filter($deductions, fn($d) => is_array($d) && stripos($d['name'] ?? '', 'Absenteeism Charge') !== false);
            $totals['totalAbsenteeismCharge'] += (float) array_sum(array_map(fn($d) => $d['amount'] ?? 0.0, $absenteeism));

            // Custom deductions (excluding statutory ones already counted)
            $customDeductions = array_filter($deductions, function ($deduction) {
                if (!is_array($deduction) || !isset($deduction['name'])) {
                    return false;
                }
                $name = strtolower($deduction['name']);
                return !in_array($name, [
                    'shif',
                    'nssf',
                    'paye',
                    'housing levy',
                    'helb',
                    'loan repayment',
                    'advance recovery',
                    'absenteeism charge'
                ]);
            });
            $totals['totalCustomDeductions'] += (float) array_sum(array_map(fn($d) => $d['amount'] ?? 0.0, $customDeductions));
        }

        return view('payroll.view', compact('business', 'payroll', 'entity', 'entityType', 'page', 'totals'));
    }

    public function downloadColumn(Request $request, $payroll_id, $column, $format)
    {
        // Fetch business slug with fallback to session
        $businessSlug = $request->route('business') ?? session('active_business_slug');
        $business = Business::findBySlug($businessSlug);

        $payroll_id = $request->id;
        $column = $request->column;
        $format = $request->format;

        if (!$business) {
            Log::error("Business not found for slug: " . ($businessSlug ?? 'Not set'));
            abort(404, 'Business not found.');
        }

        // Fetch payroll with validation
        $payroll = Payroll::where('business_id', $business->id)
            ->where('id', $payroll_id)
            ->with(['employeePayrolls.employee.user'])
            ->firstOrFail();

        // Define valid columns (consistent with view.blade.php)
        $validColumns = [
            'basic_salary',
            'gross_pay',
            'net_pay',
            'tax_no',
            'overtime',
            'shif',
            'nssf',
            'paye',
            'paye_before_reliefs',
            'housing_levy',
            'helb',
            'taxable_income',
            'personal_relief',
            'insurance_relief',
            'pay_after_tax',
            'loan_repayment',
            'advance_recovery',
            'deductions_after_tax',
            'attendance_present',
            'attendance_absent',
            'days_in_month',
            'bank_name',
            'account_number'
        ];

        $column = strtolower(trim($column));
        $format = strtolower(trim($format));
        if (!in_array($column, $validColumns)) {
            Log::warning("Invalid column name requested: {$column}");
            abort(400, 'Invalid column name.');
        }

        // Prepare data for download
        $data = $payroll->employeePayrolls->map(function ($ep) use ($column) {
            $deductions = json_decode($ep->deductions, true) ?? [];
            $overtime = json_decode($ep->overtime, true) ?? ['amount' => 0];
            $allowances = json_decode($ep->allowances, true) ?? [];

            $value = match ($column) {
                'basic_salary' => $ep->basic_salary ?? 0,
                'gross_pay' => $ep->gross_pay ?? 0,
                'net_pay' => $ep->net_pay ?? 0,
                'tax_no' => $ep->employee->tax_no ?? 'N/A',
                'overtime' => $overtime['amount'] ?? 0,
                'shif' => $ep->shif ?? ($deductions['shif'] ?? 0),
                'nssf' => $ep->nssf ?? ($deductions['nssf'] ?? 0),
                'paye' => $ep->paye ?? ($deductions['paye'] ?? 0),
                'paye_before_reliefs' => $ep->paye_before_reliefs ?? 0,
                'housing_levy' => $ep->housing_levy ?? ($deductions['housing_levy'] ?? 0),
                'helb' => $ep->helb ?? ($deductions['helb'] ?? 0),
                'taxable_income' => $ep->taxable_income ?? 0,
                'personal_relief' => $ep->personal_relief ?? 0,
                'insurance_relief' => $ep->insurance_relief ?? 0,
                'pay_after_tax' => $ep->pay_after_tax ?? 0,
                'loan_repayment' => $ep->loan_repayment ?? ($deductions['loan_repayment'] ?? 0),
                'advance_recovery' => $ep->advance_recovery ?? ($deductions['advance_recovery'] ?? 0),
                'deductions_after_tax' => $ep->deductions_after_tax ?? 0,
                'attendance_present' => $ep->attendance_present ?? 0,
                'attendance_absent' => $ep->attendance_absent ?? 0,
                'days_in_month' => $ep->days_in_month ?? 0,
                'bank_name' => $ep->bank_name ?? 'N/A',
                'account_number' => $ep->account_number ?? 'N/A',
                default => 0,
            };

            return [
                'employee_name' => $ep->employee->user->name ?? 'N/A',
                'employee_code' => $ep->employee->employee_code ?? 'N/A',
                'tax_no' => $ep->employee->tax_no ?? 'N/A',
                'basic_salary' => number_format($ep->basic_salary ?? 0, 2),
                'gross_pay' => number_format($ep->gross_pay ?? 0, 2),
                'net_pay' => number_format($ep->net_pay ?? 0, 2),
                $column => is_numeric($value) ? number_format($value, 2) : $value,
            ];
        })->toArray();

        // Generate file based on format
        $fileName = "payroll-{$payroll_id}-{$column}.{$format}";
        $currency = $payroll->currency ?? 'KES';

        switch ($format) {
            case 'pdf':
                try {
                    $pdf = Pdf::loadView('payroll.download_column', [
                        'business' => $business,
                        'payroll' => $payroll,
                        'column' => $column,
                        'data' => $data,
                        'currency' => $currency,
                    ]);
                    return $pdf->download($fileName);
                } catch (\Exception $e) {
                    Log::error("PDF generation failed for payroll {$payroll_id}, column {$column}: " . $e->getMessage());
                    abort(500, 'Failed to generate PDF.');
                }

            case 'csv':
                $csvData = "Employee Name,Employee Code,Tax Number,Basic Salary,Gross Pay,Net Pay,"
                    . ucwords(str_replace('_', ' ', $column)) . " ({$currency})\n";
                foreach ($data as $row) {
                    $csvData .= "\"" . str_replace('"', '""', $row['employee_name']) . "\","
                        . "\"" . str_replace('"', '""', $row['employee_code']) . "\","
                        . "\"" . str_replace('"', '""', $row['tax_no']) . "\","
                        . "\"" . $row['basic_salary'] . "\","
                        . "\"" . $row['gross_pay'] . "\","
                        . "\"" . $row['net_pay'] . "\","
                        . (is_numeric($row[$column]) ? $row[$column] : "\"" . str_replace('"', '""', $row[$column]) . "\"") . "\n";
                }
                return Response::make($csvData, 200, [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
                ]);

            case 'xlsx':
                try {
                    return Excel::download(new class($data, $column, $payroll) implements
                        \Maatwebsite\Excel\Concerns\FromArray,
                        \Maatwebsite\Excel\Concerns\WithHeadings {
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
                                'Tax Number',
                                'Basic Salary',
                                'Gross Pay',
                                'Net Pay',
                                ucwords(str_replace('_', ' ', $this->column)) . " (" . ($this->payroll->currency ?? 'KES') . ")",
                            ];
                        }
                    }, $fileName);
                } catch (\Maatwebsite\Excel\Exceptions\LaravelExcelException $e) {
                    Log::error("Excel generation failed for payroll {$payroll_id}, column {$column}: " . $e->getMessage());
                    abort(500, 'Failed to generate Excel file.');
                }

            default:
                Log::warning("Invalid format requested: {$format}");
                abort(400, 'Invalid format.');
        }
    }

    public function downloadReport(Request $request)
    {
        $business = Business::findBySlug(session('active_business_slug'));
        if (!$business) {
            abort(404, 'Business not found.');
        }

        $payroll = Payroll::where('business_id', $business->id)
            ->where('id', $request->payroll_id)
            ->with(['employeePayrolls.employee.user'])
            ->firstOrFail();

        $type = strtolower($request->type);
        $validTypes = ['shif', 'nssf', 'paye', 'nhdf', 'tax_filing', 'bank_advice', 'company_payslip'];

        if (!in_array($type, $validTypes)) {
            abort(404, "Invalid report type: {$type}");
        }

        // Prepare comprehensive payroll data
        $data = $payroll->employeePayrolls->map(function ($ep) {
            $deductions = json_decode($ep->deductions, true) ?? [];
            $overtime = json_decode($ep->overtime, true) ?? ['amount' => 0];
            $allowances = json_decode($ep->allowances, true) ?? [];

            return [
                'employee_name' => $ep->employee->user->name ?? 'N/A',
                'employee_code' => $ep->employee->employee_code ?? 'N/A',
                'tax_no' => $ep->employee->tax_no ?? 'N/A',
                'basic_salary' => (float) ($ep->basic_salary ?? 0),
                'gross_pay' => (float) ($ep->gross_pay ?? 0),
                'overtime' => (float) ($overtime['amount'] ?? 0),
                'shif' => (float) ($ep->shif ?? ($deductions['shif'] ?? 0)),
                'nssf' => (float) ($ep->nssf ?? ($deductions['nssf'] ?? 0)),
                'paye' => (float) ($ep->paye ?? ($deductions['paye'] ?? 0)),
                'paye_before_reliefs' => (float) ($ep->paye_before_reliefs ?? 0),
                'housing_levy' => (float) ($ep->housing_levy ?? ($deductions['housing_levy'] ?? 0)),
                'helb' => (float) ($ep->helb ?? ($deductions['helb'] ?? 0)),
                'taxable_income' => (float) ($ep->taxable_income ?? 0),
                'personal_relief' => (float) ($ep->personal_relief ?? 0),
                'insurance_relief' => (float) ($ep->insurance_relief ?? 0),
                'pay_after_tax' => (float) ($ep->pay_after_tax ?? 0),
                'loan_repayment' => (float) ($ep->loan_repayment ?? ($deductions['loan_repayment'] ?? 0)),
                'advance_recovery' => (float) ($ep->advance_recovery ?? ($deductions['advance_recovery'] ?? 0)),
                'deductions_after_tax' => (float) ($ep->deductions_after_tax ?? 0),
                'net_pay' => (float) ($ep->net_pay ?? 0),
                'attendance_present' => (int) ($ep->attendance_present ?? 0),
                'attendance_absent' => (int) ($ep->attendance_absent ?? 0),
                'days_in_month' => (int) ($ep->days_in_month ?? 0),
                'bank_name' => $ep->bank_name ?? 'N/A',
                'account_number' => $ep->account_number ?? 'N/A',
                'allowances' => $allowances,
                'deductions' => $deductions,
            ];
        })->toArray();

        $totals = [
            'totalBasicSalary' => array_sum(array_column($data, 'basic_salary')),
            'totalGrossPay' => array_sum(array_column($data, 'gross_pay')),
            'totalOvertime' => array_sum(array_column($data, 'overtime')),
            'totalShif' => array_sum(array_column($data, 'shif')),
            'totalNssf' => array_sum(array_column($data, 'nssf')),
            'totalPaye' => array_sum(array_column($data, 'paye')),
            'totalHousingLevy' => array_sum(array_column($data, 'housing_levy')),
            'totalHelb' => array_sum(array_column($data, 'helb')),
            'totalLoans' => array_sum(array_column($data, 'loan_repayment')),
            'totalAdvances' => array_sum(array_column($data, 'advance_recovery')),
            'totalNetPay' => array_sum(array_column($data, 'net_pay')),
            'totalTaxableIncome' => array_sum(array_column($data, 'taxable_income')),
            'totalPersonalRelief' => array_sum(array_column($data, 'personal_relief')),
            'totalInsuranceRelief' => array_sum(array_column($data, 'insurance_relief')),
            'totalPayAfterTax' => array_sum(array_column($data, 'pay_after_tax')),
            'totalDeductionsAfterTax' => array_sum(array_column($data, 'deductions_after_tax')),
        ];

        $entity = $payroll->location_id ? ($payroll->location ?? $business) : $business;
        $entityType = $payroll->location_id ? 'location' : 'business';

        if (!view()->exists("payroll.reports.{$type}")) {
            abort(404, "Report view for {$type} not found");
        }

        try {
            $pdf = Pdf::loadView("payroll.reports.{$type}", [
                'business' => $business,
                'payroll' => $payroll,
                'entity' => $entity,
                'entityType' => $entityType,
                'data' => $data,
                'totals' => $totals,
            ])->setPaper('a4', 'landscape');
            return $pdf->download("{$type}_report_{$payroll->payrun_year}_{$payroll->payrun_month}.pdf");
        } catch (\Exception $e) {
            \Log::error("Report generation failed for type {$type}: " . $e->getMessage());
            abort(500, 'Failed to generate report.');
        }
    }

    public function sendPayslips(Request $request)
    {
        $payrollId = $request->input('payroll_id');
        $employeePayrollId = $request->input('employee_payroll_id');

        if (!$payrollId && !$employeePayrollId) {
            return RequestResponse::badRequest('Either payroll_id or employee_payroll_id is required.');
        }

        return $this->handleTransaction(function () use ($request, $payrollId, $employeePayrollId) {
            $business = Business::findBySlug(session('active_business_slug'));
            if (!$business) {
                return RequestResponse::badRequest('Business not found.');
            }

            // Determine the scope: single payslip or all for a payroll
            if ($employeePayrollId) {
                $employeePayroll = EmployeePayroll::with(['employee.user', 'payroll.business', 'payroll.location'])
                    ->where('id', $employeePayrollId)
                    ->whereHas('payroll', fn($q) => $q->where('business_id', $business->id))
                    ->firstOrFail();

                $employeePayrolls = collect([$employeePayroll]); // Single item collection
                $payroll = $employeePayroll->payroll;
            } else {
                $payroll = Payroll::where('business_id', $business->id)
                    ->where('id', $payrollId)
                    ->with(['employeePayrolls.employee.user'])
                    ->firstOrFail();

                $employeePayrolls = $payroll->employeePayrolls;
            }

            // Process each payslip
            $sentCount = 0;
            foreach ($employeePayrolls as $employeePayroll) {
                $user = $employeePayroll->employee->user;
                if (!$user || !$user->email) {
                    Log::warning("No email found for employee ID: {$employeePayroll->employee_id}, skipping payslip.");
                    continue;
                }

                // Determine entity for the payslip view
                $entity = $business;
                $entityType = 'business';
                if ($employeePayroll->payroll->location_id) {
                    $location = Location::where('id', $employeePayroll->payroll->location_id)
                        ->where('business_id', $business->id)
                        ->first();
                    if ($location) {
                        $entity = $location;
                        $entityType = 'location';
                    }
                }

                // Generate PDF
                $pdf = Pdf::loadView('payroll.reports.payslip', compact('employeePayroll', 'business', 'entity', 'entityType'));
                $fileName = 'payslip_' . $employeePayroll->id . '_' . time() . '.pdf';
                $filePath = storage_path('app/public/payslips/' . $fileName);

                if (!file_exists(storage_path('app/public/payslips'))) {
                    mkdir(storage_path('app/public/payslips'), 0755, true);
                }
                $pdf->save($filePath);

                // Send email
                Mail::to($user->email)->send(new PayslipMail($employeePayroll, $filePath, $user->name));
                $sentCount++;
            }

            // Update payroll status if sending all payslips
            if ($payrollId && !$employeePayrollId) {
                $payroll->update(['emailed' => true]);
            }

            $message = $employeePayrollId
                ? 'Payslip queued for sending.'
                : "Payslips queued for sending ($sentCount sent).";
            return RequestResponse::ok($message, ['sent_count' => $sentCount]);
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
            }])
            ->latest('updated_at');

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