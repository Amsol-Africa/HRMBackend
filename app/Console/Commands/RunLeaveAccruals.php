<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Business;
use App\Models\LeavePeriod;
use App\Services\Leave\LeaveAccrualService;

class RunLeaveAccruals extends Command
{
    protected $signature = 'leave:run-accruals
                            {--business= : Business slug to scope accruals}
                            {--period=   : Leave period slug to scope accruals}
                            {--dry-run   : Compute without saving changes}';

    protected $description = 'Run leave accruals for entitlements (monthly/quarterly/yearly), recomputing totals and remaining days.';

    public function handle(LeaveAccrualService $service): int
    {
        $business = null;
        $period = null;

        if ($slug = $this->option('business')) {
            $business = Business::findBySlug($slug);
            if (!$business) {
                $this->error("Business not found for slug: {$slug}");
                return self::FAILURE;
            }
        }

        if ($slug = $this->option('period')) {
            $period = LeavePeriod::where('slug', $slug)->first();
            if (!$period) {
                $this->error("LeavePeriod not found for slug: {$slug}");
                return self::FAILURE;
            }
        }

        $dryRun = (bool)$this->option('dry-run');

        $this->info('Running leave accruals...');
        $result = $service->run($business, $period, $dryRun);

        $this->line("Processed entitlements: {$result['processed']}");
        $this->line("Total days accrued:     {$result['accrued']}");

        // Optional verbose dump
        foreach ($result['details'] as $row) {
            $this->line(json_encode($row));
        }

        $this->info($dryRun ? 'Dry run complete. No changes saved.' : 'Accrual run complete.');
        return self::SUCCESS;
    }
}
