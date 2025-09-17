<?php

namespace App\Services\Leave;

use App\Models\Business;
use App\Models\LeaveEntitlement;
use App\Models\LeavePeriod;
use App\Models\LeavePolicy;
use App\Models\LeaveType;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class LeaveAccrualService
{
    /**
     * Run accruals across entitlements, optionally scoped by business/period.
     *
     * @param Business|null     $business     Scope to a single business (optional)
     * @param LeavePeriod|null  $leavePeriod  Scope to a single period (recommended)
     * @param bool              $dryRun       If true, compute without saving
     * @return array{processed:int, accrued:float, details:array}
     */
    public function run(?Business $business = null, ?LeavePeriod $leavePeriod = null, bool $dryRun = false): array
    {
        $query = LeaveEntitlement::with([
            'leaveType.leavePolicies',
            'employee.department',
            'employee.jobCategory',
            'leavePeriod',
            'business',
        ]);

        if ($business) {
            $query->where('business_id', $business->id);
        }
        if ($leavePeriod) {
            $query->where('leave_period_id', $leavePeriod->id);
        }

        /** @var Collection<int, LeaveEntitlement> $entitlements */
        $entitlements = $query->get();

        $totalAccrued = 0.0;
        $processed = 0;
        $details = [];

        foreach ($entitlements as $entitlement) {
            $policy = $this->findMatchingPolicy($entitlement);
            if (!$policy) {
                $details[] = [
                    'entitlement_id' => $entitlement->id,
                    'status' => 'skipped',
                    'reason' => 'No matching policy found',
                ];
                continue;
            }

            // Determine window to accrue within
            $periodStart = Carbon::parse($entitlement->leavePeriod->start_date ?? now()->startOfYear())->startOfDay();
            $periodEnd   = Carbon::parse($entitlement->leavePeriod->end_date   ?? now()->endOfYear())->endOfDay();

            // Anchor start: last accrued or period start, whichever is later
            $anchor = $entitlement->last_accrued_at
                ? $entitlement->last_accrued_at->copy()->startOfDay()
                : $periodStart->copy();

            // If anchor is already beyond period end, nothing to do
            if ($anchor->gte($periodEnd)) {
                $details[] = [
                    'entitlement_id' => $entitlement->id,
                    'status' => 'skipped',
                    'reason' => 'Accrual already up-to-date for period',
                ];
                continue;
            }

            $now = now();
            $windowEnd = $now->lt($periodEnd) ? $now : $periodEnd;

            $intervals = $this->countIntervalsDue(
                $policy->accrual_frequency,
                $anchor,
                $windowEnd
            );

            if ($intervals <= 0) {
                $details[] = [
                    'entitlement_id' => $entitlement->id,
                    'status' => 'skipped',
                    'reason' => 'No intervals due yet',
                ];
                continue;
            }

            $increment = (float)$policy->accrual_amount * (float)$intervals;
            $newAccrued = (float)($entitlement->accrued_days ?? 0) + $increment;

            $details[] = [
                'entitlement_id' => $entitlement->id,
                'status' => $dryRun ? 'dry-run' : 'accrued',
                'intervals' => $intervals,
                'frequency' => $policy->accrual_frequency,
                'accrual_amount' => (float)$policy->accrual_amount,
                'increment' => $increment,
                'prev_accrued_days' => (float)($entitlement->accrued_days ?? 0),
                'new_accrued_days' => $newAccrued,
            ];

            if (!$dryRun) {
                $entitlement->accrued_days = $newAccrued;
                // Advance last_accrued_at by "intervals" of the policy frequency from anchor
                $entitlement->last_accrued_at = $this->advanceByIntervals($anchor->copy(), $policy->accrual_frequency, $intervals);
                $entitlement->calculateRemainingDays(); // recomputes total_days and days_remaining
            }

            $totalAccrued += $increment;
            $processed++;
        }

        return [
            'processed' => $processed,
            'accrued'   => $totalAccrued,
            'details'   => $details,
        ];
    }

    /**
     * Try to find a policy that matches the employee (department, job category, gender).
     * Fallbacks:
     *  - gender 'all'
     *  - policy with null dept/category
     *  - any policy for the leave type (first one)
     */
    protected function findMatchingPolicy(LeaveEntitlement $entitlement): ?LeavePolicy
    {
        $leaveType = $entitlement->leaveType;
        if (!$leaveType) {
            return null;
        }

        $policies = $leaveType->leavePolicies; // collection
        if ($policies->isEmpty()) {
            return null;
        }

        $employee = $entitlement->employee;
        $deptId = $employee->department_id ?? null;
        $jobCatId = $employee->job_category_id ?? null;
        $gender = strtolower($employee->gender ?? 'all');

        // 1) exact match
        $match = $policies->first(function (LeavePolicy $p) use ($deptId, $jobCatId, $gender) {
            $g = strtolower($p->gender_applicable ?? 'all');
            return (int)$p->department_id === (int)$deptId
                && (int)$p->job_category_id === (int)$jobCatId
                && ($g === 'all' || $g === $gender);
        });
        if ($match) return $match;

        // 2) dept/job exact, gender all
        $match = $policies->first(function (LeavePolicy $p) use ($deptId, $jobCatId) {
            $g = strtolower($p->gender_applicable ?? 'all');
            return (int)$p->department_id === (int)$deptId
                && (int)$p->job_category_id === (int)$jobCatId
                && $g === 'all';
        });
        if ($match) return $match;

        // 3) dept null or job_cat null with gender match/all
        $match = $policies->first(function (LeavePolicy $p) use ($deptId, $jobCatId, $gender) {
            $g = strtolower($p->gender_applicable ?? 'all');
            $genderOk = ($g === 'all' || $g === $gender);
            $deptOk = (empty($p->department_id) || (int)$p->department_id === (int)$deptId);
            $jobOk  = (empty($p->job_category_id) || (int)$p->job_category_id === (int)$jobCatId);
            return $deptOk && $jobOk && $genderOk;
        });
        if ($match) return $match;

        // 4) fallback: first policy
        return $policies->first();
    }

    /**
     * Count how many whole intervals (monthly/quarterly/yearly) are due between $from (exclusive) and $to (inclusive).
     */
    protected function countIntervalsDue(string $frequency, Carbon $from, Carbon $to): int
    {
        $frequency = strtolower($frequency);
        if ($to->lte($from)) {
            return 0;
        }

        // Normalize to period boundaries (we accrue on period boundaries after $from)
        switch ($frequency) {
            case 'monthly':
                // Next month boundary after $from
                $cursor = $from->copy()->startOfDay()->addMonthNoOverflow()->startOfMonth();
                $count = 0;
                while ($cursor->lte($to)) {
                    $count++;
                    $cursor->addMonthNoOverflow()->startOfMonth();
                }
                return $count;

            case 'quarterly':
                // Quarters start: Jan/Apr/Jul/Oct
                $cursor = $this->nextQuarterStartAfter($from);
                $count = 0;
                while ($cursor->lte($to)) {
                    $count++;
                    $cursor = $cursor->copy()->addMonthsNoOverflow(3)->startOfMonth();
                }
                return $count;

            case 'yearly':
                $cursor = $from->copy()->startOfDay()->addYear()->startOfYear();
                $count = 0;
                while ($cursor->lte($to)) {
                    $count++;
                    $cursor->addYear()->startOfYear();
                }
                return $count;

            default:
                // Unknown frequency: do nothing
                return 0;
        }
    }

    /**
     * Advance a date forward by $intervals intervals of $frequency.
     */
    protected function advanceByIntervals(Carbon $date, string $frequency, int $intervals): Carbon
    {
        $frequency = strtolower($frequency);
        return match ($frequency) {
            'monthly'   => $date->addMonthsNoOverflow($intervals),
            'quarterly' => $date->addMonthsNoOverflow(3 * $intervals),
            'yearly'    => $date->addYears($intervals),
            default     => $date,
        };
    }

    /**
     * Get the start of the next quarter after a given date.
     */
    protected function nextQuarterStartAfter(Carbon $date): Carbon
    {
        // Determine current quarter (1..4)
        $month = (int)$date->month;
        $currentQuarterStartMonth = [1, 4, 7, 10][intdiv($month - 1, 3)];
        $currentQuarterStart = Carbon::create($date->year, $currentQuarterStartMonth, 1)->startOfDay();

        // If we're already at/beyond the start this quarter, move to next quarter start
        if ($date->gte($currentQuarterStart)) {
            $nextStart = $currentQuarterStart->copy()->addMonthsNoOverflow(3);
            return $nextStart->startOfMonth()->startOfDay();
        }

        // Otherwise accrual boundary is the current quarter start (since date is before it)
        return $currentQuarterStart->startOfDay();
    }
}
