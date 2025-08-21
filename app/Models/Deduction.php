<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\ModelStatus\HasStatuses;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Deduction extends Model
{
    use HasFactory, HasStatuses, HasSlug, LogsActivity;

    protected $fillable = [
        'business_id',
        'location_id',           // Kept from original model
        'name',
        'slug',
        'description',
        'calculation_basis',     // basic_pay, gross_pay, etc.
        'computation_method',    // fixed, rate, formula (replaces type)
        'amount',                // Fixed amount
        'rate',                  // Percentage rate
        'formula',               // Custom formula
        'actual_amount',         // Boolean for employee-specific amounts
        'fraction_to_consider',  // employee_only or employee_and_employer
        'limit',                 // Maximum deduction cap
        'round_off',             // round_off_up or round_off_down
        'decimal_places',        // Precision for rounding
        'is_statutory',          // Statutory flag
        'is_optional',           // Optional flag
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'rate' => 'decimal:2',
        'limit' => 'decimal:2',
        'decimal_places' => 'integer',
        'actual_amount' => 'boolean',
        'is_statutory' => 'boolean',
        'is_optional' => 'boolean',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function employeeDeductions()
    {
        return $this->hasMany(EmployeeDeduction::class);
    }

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'employee_deductions')
            ->withPivot('amount', 'is_active')
            ->withTimestamps();
    }

    /**
     * Calculate the deduction amount based on the computation method.
     *
     * @param float $baseAmount The amount to compute deduction against (e.g., gross_pay)
     * @param float|null $employeeSpecificAmount Optional employee-specific amount
     * @return float The calculated deduction
     */
    public function calculate(float $baseAmount, ?float $employeeSpecificAmount = null): float
    {
        $deduction = 0;

        switch ($this->computation_method) {
            case 'fixed':
                $deduction = $this->amount ?? 0;
                break;

            case 'rate':
                $deduction = ($baseAmount * ($this->rate / 100)) ?? 0;
                break;

            case 'formula':
                if ($this->formula) {
                    // Simplified formula evaluation (e.g., "base * 0.05")
                    // Use a proper parser like symfony/expression-language in production
                    $deduction = eval("return {$baseAmount} * " . str_replace('base', '$baseAmount', $this->formula) . ";");
                }
                break;
        }

        // Use employee-specific amount if actual_amount is true and provided
        if ($this->actual_amount && $employeeSpecificAmount !== null) {
            $deduction = $employeeSpecificAmount;
        }

        // Apply limit
        if ($this->limit !== null) {
            $deduction = min($deduction, $this->limit);
        }

        // Apply rounding
        if ($this->round_off === 'round_off_up') {
            $deduction = ceil($deduction * pow(10, $this->decimal_places)) / pow(10, $this->decimal_places);
        } elseif ($this->round_off === 'round_off_down') {
            $deduction = floor($deduction * pow(10, $this->decimal_places)) / pow(10, $this->decimal_places);
        }

        return $deduction;
    }

    /**
     * Get deduction amount for a specific employee.
     *
     * @param Employee $employee
     * @param float $baseAmount
     * @return float
     */
    public function getEmployeeDeduction(Employee $employee, float $baseAmount): float
    {
        $pivot = $this->employees()->where('employee_id', $employee->id)->first();
        $employeeAmount = $pivot ? $pivot->pivot->amount : null;

        return $this->calculate($baseAmount, $employeeAmount);
    }
}
