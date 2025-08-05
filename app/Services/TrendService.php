<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class TrendService
{
    public function getTrends(Model $model, $businessId, $year = null, $dateColumn = 'created_at', $sumColumns = [])
    {
        $cacheKey = "trends_" . class_basename($model) . "_{$businessId}_{$year}";

        return Cache::remember($cacheKey, now()->addHours(6), function () use ($model, $businessId, $year, $dateColumn, $sumColumns) {
            $query = $model->whereHas('employee', function ($q) use ($businessId) {
                $q->where('business_id', $businessId);
            })->selectRaw("DATE_FORMAT({$dateColumn}, '%Y-%m') as month");

            $sumColumns = is_array($sumColumns) ? $sumColumns : ($sumColumns ? [$sumColumns] : []);

            foreach ($sumColumns as $column) {
                $query->addSelect(DB::raw("SUM({$column}) as total_{$column}"));
            }

            $query->groupBy('month')->orderBy('month');

            if ($year) {
                $query->whereYear($dateColumn, $year);
            }

            return $query->get();
        });
    }

}
