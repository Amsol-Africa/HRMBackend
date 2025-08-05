<?php

namespace App\Console\Commands;

use App\Models\Kpi;
use Illuminate\Console\Command;

class CalculateKpis extends Command
{
    protected $signature = 'kpis:calculate';
    protected $description = 'Calculate KPIs for all relevant models';

    public function handle()
    {
        $kpis = Kpi::all();

        foreach ($kpis as $kpi) {
            $modelClass = $kpi->model_type;
            $instances = $modelClass::all();

            foreach ($instances as $instance) {
                $kpi->calculate($instance);
                $this->info("Calculated {$kpi->name} for {$modelClass} ID {$instance->id}");
            }
        }

        $this->info('KPI calculation completed.');
    }
}