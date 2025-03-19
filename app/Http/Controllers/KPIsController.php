<?php

namespace App\Http\Controllers;

use App\Models\Kpi;
use App\Models\KpiResult;
use App\Models\Business;
use Illuminate\Http\Request;
use App\Http\RequestResponse;
use App\Traits\HandleTransactions;
use Illuminate\Support\Facades\Log;

class KpisController extends Controller
{
    use HandleTransactions;

    public function index(Request $request)
    {
        $business = Business::findBySlug(session('active_business_slug'));
        if (!$business) {
            return redirect()->route('business.select')->with('error', 'No active business selected.');
        }
        return view('kpis.index');
    }

    public function fetch(Request $request)
    {
        $business = Business::findBySlug(session('active_business_slug'));
        if (!$business) {
            return RequestResponse::badRequest('No active business selected.');
        }

        $kpis = Kpi::whereIn('model_type', [
            'App\Models\Attendance',
            'App\Models\Application',
            'App\Models\EmployeePayroll',
            'App\Models\Overtime',
            'App\Models\LeaveRequest',
            'App\Models\Task',
            'App\Models\Advance',
            'App\Models\Loan',
            'App\Models\JobPost',
        ])->with(['results' => function ($query) use ($business) {
            $query->whereHasMorph('model', ['*'], function ($q) use ($business) {
                $this->applyBusinessFilter($q, $business->id);
            });
        }])->get();

        foreach ($kpis as $kpi) {
            if ($kpi->results->isEmpty()) {
                $modelClass = $kpi->model_type;
                $instances = $this->getInstancesForModel($modelClass, $business->id);

                Log::info("Calculating KPI: {$kpi->name}", [
                    'model_type' => $modelClass,
                    'instance_count' => $instances->count(),
                ]);

                if ($instances->isEmpty()) {
                    Log::warning("No instances found for {$modelClass} in business {$business->id}");
                    continue;
                }

                foreach ($instances as $instance) {
                    $kpi->calculate($instance);
                }
                $kpi->load('results');
            }
        }

        $kpi_cards = view('kpis._cards', compact('kpis'))->render();
        return RequestResponse::ok('KPIs fetched successfully.', $kpi_cards);
    }

    public function results(Request $request)
    {
        $validatedData = $request->validate([
            'kpi_slug' => 'required|exists:kpis,slug',
        ]);

        $business = Business::findBySlug(session('active_business_slug'));
        $kpi = Kpi::where('slug', $validatedData['kpi_slug'])->with(['results' => function ($query) use ($business) {
            $query->whereHasMorph('model', ['*'], fn($q) => $this->applyBusinessFilter($q, $business->id));
        }])->firstOrFail();
        $results_view = view('kpis._results', compact('kpi'))->render();

        return RequestResponse::ok('KPI results fetched successfully.', $results_view);
    }

    public function create()
    {
        $modelTypes = [
            'App\Models\Attendance' => 'Attendance',
            'App\Models\Application' => 'Job Applications',
            'App\Models\EmployeePayroll' => 'Payroll',
            'App\Models\Overtime' => 'Overtime',
            'App\Models\LeaveRequest' => 'Leave Requests',
            'App\Models\Task' => 'Tasks',
            'App\Models\Advance' => 'Advances',
            'App\Models\Loan' => 'Loans',
            'App\Models\JobPost' => 'Job Posts',
        ];
        $calculationMethods = ['percentage', 'count', 'average', 'sum', 'ratio'];
        $comparisonOperators = ['>=', '<=', '='];

        $kpi_form = view('kpis._form', compact('modelTypes', 'calculationMethods', 'comparisonOperators'))->render();
        return RequestResponse::ok('KPI form loaded.', $kpi_form);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:kpis,slug|max:255',
            'model_type' => 'required|string|in:App\Models\Attendance,App\Models\Application,App\Models\EmployeePayroll,App\Models\Overtime,App\Models\LeaveRequest,App\Models\Task,App\Models\Advance,App\Models\Loan,App\Models\JobPost',
            'description' => 'nullable|string',
            'calculation_method' => 'required|string|in:percentage,count,average,sum,ratio',
            'target_value' => 'required|numeric',
            'comparison_operator' => 'required|string|in:>=,<=,=',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $kpi = Kpi::create($validatedData);
            return RequestResponse::created('KPI created successfully.');
        });
    }

    public function calculate(Request $request)
    {
        $validatedData = $request->validate([
            'model_type' => 'required|string|in:App\Models\Attendance,App\Models\Application,App\Models\EmployeePayroll,App\Models\Overtime,App\Models\LeaveRequest,App\Models\Task,App\Models\Advance,App\Models\Loan,App\Models\JobPost',
            'model_id' => 'required|integer|exists:' . $this->getTableFromModel($request->input('model_type')) . ',id',
            'kpi_slug' => 'nullable|exists:kpis,slug',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $modelClass = $validatedData['model_type'];
            $instance = $modelClass::findOrFail($validatedData['model_id']);
            $business = Business::findBySlug(session('active_business_slug'));

            if (!$this->instanceBelongsToBusiness($instance, $business->id)) {
                return RequestResponse::forbidden('Model instance does not belong to the active business.');
            }

            $kpis = $validatedData['kpi_slug']
                ? Kpi::where('slug', $validatedData['kpi_slug'])->get()
                : Kpi::where('model_type', $validatedData['model_type'])->get();

            $results = [];
            foreach ($kpis as $kpi) {
                $result = $kpi->calculate($instance);
                $results[] = [
                    'kpi_name' => $kpi->name,
                    'result_value' => $result->result_value,
                    'meets_target' => $result->meets_target,
                ];
            }

            $results_view = view('kpis._calculation_results', compact('results'))->render();
            return RequestResponse::ok('KPI calculated successfully.', $results_view);
        });
    }

    public function edit(Request $request)
    {
        $validatedData = $request->validate([
            'kpi_slug' => 'required|exists:kpis,slug',
        ]);

        $kpi = Kpi::where('slug', $validatedData['kpi_slug'])->firstOrFail();
        $modelTypes = [
            'App\Models\Attendance' => 'Attendance',
            'App\Models\Application' => 'Job Applications',
            'App\Models\EmployeePayroll' => 'Payroll',
            'App\Models\Overtime' => 'Overtime',
            'App\Models\LeaveRequest' => 'Leave Requests',
            'App\Models\Task' => 'Tasks',
            'App\Models\Advance' => 'Advances',
            'App\Models\Loan' => 'Loans',
            'App\Models\JobPost' => 'Job Posts',
        ];
        $calculationMethods = ['percentage', 'count', 'average', 'sum', 'ratio'];
        $comparisonOperators = ['>=', '<=', '='];

        $kpi_form = view('kpis._form', compact('kpi', 'modelTypes', 'calculationMethods', 'comparisonOperators'))->render();
        return RequestResponse::ok('KPI found.', $kpi_form);
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'kpi_slug' => 'required|exists:kpis,slug',
            'name' => 'required|string|max:255',
            'model_type' => 'required|string|in:App\Models\Attendance,App\Models\Application,App\Models\EmployeePayroll,App\Models\Overtime,App\Models\LeaveRequest,App\Models\Task,App\Models\Advance,App\Models\Loan,App\Models\JobPost',
            'description' => 'nullable|string',
            'calculation_method' => 'required|string|in:percentage,count,average,sum,ratio',
            'target_value' => 'required|numeric',
            'comparison_operator' => 'required|string|in:>=,<=,=',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $kpi = Kpi::where('slug', $validatedData['kpi_slug'])->firstOrFail();
            $kpi->update([
                'name' => $validatedData['name'],
                'model_type' => $validatedData['model_type'],
                'description' => $validatedData['description'] ?? null,
                'calculation_method' => $validatedData['calculation_method'],
                'target_value' => $validatedData['target_value'],
                'comparison_operator' => $validatedData['comparison_operator'],
            ]);

            return RequestResponse::ok('KPI updated successfully.');
        });
    }

    public function destroy(Request $request)
    {
        $validatedData = $request->validate([
            'kpi_slug' => 'required|exists:kpis,slug',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $kpi = Kpi::where('slug', $validatedData['kpi_slug'])->firstOrFail();
            $kpi->delete();
            return RequestResponse::ok('KPI deleted successfully.');
        });
    }

    private function getTableFromModel($modelType)
    {
        $modelMap = [
            'App\Models\Attendance' => 'attendances',
            'App\Models\Application' => 'applications',
            'App\Models\EmployeePayroll' => 'employee_payrolls',
            'App\Models\Overtime' => 'overtimes',
            'App\Models\LeaveRequest' => 'leave_requests',
            'App\Models\Task' => 'tasks',
            'App\Models\Advance' => 'advances',
            'App\Models\Loan' => 'loans',
            'App\Models\JobPost' => 'job_posts',
        ];

        return $modelMap[$modelType] ?? '';
    }

    private function getInstancesForModel($modelClass, $businessId)
    {
        switch ($modelClass) {
            case 'App\Models\EmployeePayroll':
                return $modelClass::whereHas('payroll', function ($query) use ($businessId) {
                    $query->where('business_id', $businessId);
                })->get();
            case 'App\Models\Advance':
            case 'App\Models\Loan':
                return $modelClass::whereHas('employee', function ($query) use ($businessId) {
                    $query->where('business_id', $businessId);
                })->get();
            case 'App\Models\Attendance':
            case 'App\Models\Application':
            case 'App\Models\Overtime':
            case 'App\Models\LeaveRequest':
            case 'App\Models\Task':
            case 'App\Models\JobPost':
                return $modelClass::where('business_id', $businessId)->get();
            default:
                Log::warning("Unsupported model type: {$modelClass}");
                return collect();
        }
    }

    private function applyBusinessFilter($query, $businessId)
    {
        $query->where(function ($q) use ($businessId) {
            $q->where('model_type', 'App\Models\EmployeePayroll')
                ->whereExists(function ($subQuery) use ($businessId) {
                    $subQuery->selectRaw(1)
                        ->from('payrolls')
                        ->whereColumn('payrolls.id', 'kpi_results.model_id')
                        ->where('payrolls.business_id', $businessId);
                })
                ->orWhere(function ($subQ) use ($businessId) {
                    $subQ->whereIn('model_type', ['App\Models\Advance', 'App\Models\Loan'])
                        ->whereExists(function ($existsQuery) use ($businessId) {
                            $table = $this->getTableFromModel($existsQuery->from === 'advances' ? 'App\Models\Advance' : 'App\Models\Loan');
                            $existsQuery->selectRaw(1)
                                ->from($table)
                                ->join('employees', "$table.employee_id", '=', 'employees.id')
                                ->whereColumn("$table.id", 'kpi_results.model_id')
                                ->where('employees.business_id', $businessId);
                        });
                })
                ->orWhere(function ($subQ) use ($businessId) {
                    $modelTypes = [
                        'App\Models\Attendance' => 'attendances',
                        'App\Models\Application' => 'applications',
                        'App\Models\Overtime' => 'overtimes',
                        'App\Models\LeaveRequest' => 'leave_requests',
                        'App\Models\Task' => 'tasks',
                        'App\Models\JobPost' => 'job_posts',
                    ];

                    foreach ($modelTypes as $modelType => $table) {
                        $subQ->orWhere(function ($innerQ) use ($modelType, $table, $businessId) {
                            $innerQ->where('model_type', $modelType)
                                ->whereExists(function ($existsQuery) use ($table, $businessId) {
                                    $existsQuery->selectRaw(1)
                                        ->from($table)
                                        ->whereColumn("$table.id", 'kpi_results.model_id')
                                        ->where("$table.business_id", $businessId);
                                });
                        });
                    }
                });
        });
    }

    private function instanceBelongsToBusiness($instance, $businessId)
    {
        if ($instance instanceof \App\Models\EmployeePayroll) {
            return $instance->payroll && $instance->payroll->business_id === $businessId;
        }
        if ($instance instanceof \App\Models\Advance || $instance instanceof \App\Models\Loan) {
            return $instance->employee && $instance->employee->business_id === $businessId;
        }
        return property_exists($instance, 'business_id') && $instance->business_id === $businessId;
    }
}