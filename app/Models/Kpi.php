<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Attendance;
use App\Models\Application;
use App\Models\EmployeePayroll;
use App\Models\Overtime;
use App\Models\LeaveRequest;
use App\Models\Task;
use App\Models\Advance;
use App\Models\Loan;
use App\Models\JobPost;

class Kpi extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'model_type',
        'description',
        'calculation_method',
        'target_value',
        'comparison_operator',
    ];

    public function results()
    {
        return $this->hasMany(KpiResult::class);
    }

    public function calculate($modelInstance)
    {
        $method = ucfirst($this->calculation_method);
        $value = $this->{"calculate{$method}"}($modelInstance);

        $result = new KpiResult([
            'kpi_id' => $this->id,
            'model_type' => $this->model_type,
            'model_id' => $modelInstance->id,
            'result_value' => $value,
            'meets_target' => $this->evaluateTarget($value),
            'measured_at' => now()->toDateString(),
        ]);
        $result->save();

        return $result;
    }

    protected function evaluateTarget($value)
    {
        $target = (float) $this->target_value;
        switch ($this->comparison_operator) {
            case '>=':
                return $value >= $target;
            case '<=':
                return $value <= $target;
            case '=':
                return $value == $target;
            default:
                return false;
        }
    }

    protected function calculatePercentage($modelInstance)
    {
        if ($this->model_type === 'App\Models\Attendance') {
            $employeeId = $modelInstance->employee_id;
            $businessId = $modelInstance->business_id;
            $month = $modelInstance->date->startOfMonth();

            $totalDays = $month->daysInMonth;
            $presentDays = Attendance::where('employee_id', $employeeId)
                ->where('business_id', $businessId)
                ->whereBetween('date', [$month, $month->endOfMonth()])
                ->where('is_absent', 0)
                ->count();

            return $totalDays > 0 ? ($presentDays / $totalDays) * 100 : 0;
        }
        return 0;
    }

    protected function calculateCount($modelInstance)
    {
        if ($this->model_type === 'App\Models\Application') {
            return Application::where('job_post_id', $modelInstance->job_post_id)->count();
        } elseif ($this->model_type === 'App\Models\Task') {
            return Task::where('business_id', $modelInstance->business_id)
                ->where('due_date', '<=', now())
                ->whereHas('statuses', fn($q) => $q->where('name', 'completed'))
                ->count();
        }
        return 0;
    }

    protected function calculateAverage($modelInstance)
    {
        if ($this->model_type === 'App\Models\EmployeePayroll') {
            return EmployeePayroll::where('payroll_id', $modelInstance->payroll_id)
                ->avg('net_pay') ?? 0;
        } elseif ($this->model_type === 'App\Models\Advance') {
            return Advance::where('employee_id', $modelInstance->employee_id)
                ->avg('amount') ?? 0;
        } elseif ($this->model_type === 'App\Models\Loan') {
            return Loan::where('employee_id', $modelInstance->employee_id)
                ->avg('amount') ?? 0;
        }
        return 0;
    }

    protected function calculateSum($modelInstance)
    {
        if ($this->model_type === 'App\Models\Overtime') {
            $month = $modelInstance->date->startOfMonth();
            return Overtime::where('employee_id', $modelInstance->employee_id)
                ->whereBetween('date', [$month, $month->endOfMonth()])
                ->sum('overtime_hours') ?? 0;
        } elseif ($this->model_type === 'App\Models\LeaveRequest') {
            return LeaveRequest::where('employee_id', $modelInstance->employee_id)
                ->where('start_date', '>=', now()->startOfYear())
                ->sum('total_days') ?? 0;
        }
        return 0;
    }

    protected function calculateRatio($modelInstance)
    {
        if ($this->model_type === 'App\Models\JobPost') {
            $applications = Application::where('job_post_id', $modelInstance->id)->count();
            $targetHires = $modelInstance->vacancies ?? 1;
            return $targetHires > 0 ? $applications / $targetHires : 0;
        }
        return 0;
    }

    public function getIconClass()
    {
        return match ($this->model_type) {
            'App\Models\Attendance' => 'fas fa-calendar-check',
            'App\Models\Application' => 'fas fa-file-alt',
            'App\Models\EmployeePayroll' => 'fas fa-money-check-alt',
            'App\Models\Overtime' => 'fas fa-clock',
            'App\Models\LeaveRequest' => 'fas fa-plane-departure',
            'App\Models\Task' => 'fas fa-tasks',
            'App\Models\Advance' => 'fas fa-hand-holding-usd',
            'App\Models\Loan' => 'fas fa-piggy-bank',
            'App\Models\JobPost' => 'fas fa-briefcase',
            default => 'fas fa-chart-line',
        };
    }

    public function getProgressPercentage()
    {
        if ($this->results->isEmpty()) {
            return 0;
        }

        $result = $this->results->last()->result_value;
        $target = (float) $this->target_value;

        // Handle different comparison operators
        if ($this->comparison_operator === '>=') {
            return min(100, ($result / $target) * 100);
        } elseif ($this->comparison_operator === '<=') {
            return min(100, (($target - $result) / $target) * 100 + 50); // Simplified; adjust logic as needed
        } elseif ($this->comparison_operator === '=') {
            return abs($result - $target) < 0.01 ? 100 : min(100, ($result / $target) * 100);
        }
        return 0;
    }
}