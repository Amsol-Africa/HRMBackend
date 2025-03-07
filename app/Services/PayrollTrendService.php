<?php

namespace App\Services;

use App\Models\EmployeePayroll;
use Illuminate\Support\Facades\Cache;

class PayrollTrendService
{
    public function getTrends($businessId, $year = null)
    {
        return Cache::remember("payroll_trends_{$businessId}_{$year}", now()->addHours(6), function () use ($businessId, $year) {
            $query = EmployeePayroll::whereHas('employee', function ($q) use ($businessId) {
                    $q->where('business_id', $businessId);
                })
                ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, SUM(net_pay) as total_net_pay, SUM(gross_pay) as total_gross_pay")
                ->groupBy('month')
                ->orderBy('month');

            if ($year) {
                $query->whereYear('created_at', $year);
            }

            return $query->get();
        });
    }
}
