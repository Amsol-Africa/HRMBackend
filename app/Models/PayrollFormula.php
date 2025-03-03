<?php

namespace App\Models;

use Exception;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PayrollFormula extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'business_id',
        'name',
        'formula_type',
        'slug',
        'calculation_basis',
        'is_progressive',
        'minimum_amount',
    ];

    public function brackets()
    {
        return $this->hasMany(PayrollFormulaBracket::class);
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom(fieldName: 'name')->saveSlugsTo('slug');
    }
    public static function calculateForBusiness($formulaName, $amount, $business_id = null)
    {
        // Attempt to get the formula specific to the business
        $formulaQuery = self::where('name', 'LIKE', "%{$formulaName}%");

        if ($business_id) {
            $businessFormula = (clone $formulaQuery)->where('business_id', $business_id)->first();
            if ($businessFormula) {
                return $businessFormula->is_progressive
                    ? self::calculateProgressive($amount, $businessFormula->brackets)
                    : self::calculateFlat($amount, $businessFormula);
            }
        }

        // If no business-specific formula exists, fallback to system-wide formula
        $systemFormula = $formulaQuery->whereNull('business_id')->first();
        if (!$systemFormula) {
            return 0; // No formula found, return zero deduction
        }

        return $systemFormula->is_progressive
            ? self::calculateProgressive($amount, $systemFormula->brackets)
            : self::calculateFlat($amount, $systemFormula);
    }

    private static function calculateFlat($amount, $formula)
    {
        if ($formula->formula_type === 'rate') {
            return ($amount * ($formula->minimum_amount / 100));
        }

        return $formula->minimum_amount;
    }

    private static function calculateProgressive($amount, $brackets)
    {
        $tax = 0;

        foreach ($brackets as $bracket) {
            $min = $bracket['min'];
            $max = $bracket['max'] ?? null;
            $rate = $bracket['rate'] ?? 0;
            $fixed = $bracket['amount'] ?? 0;

            if ($amount > $min) {
                $taxableAmount = $max ? min($amount, $max) - $min : $amount - $min;
                $tax += ($taxableAmount * ($rate / 100)) + $fixed;
            }
        }

        return $tax;
    }

    public static function getFixedAmount(string $slug)
    {
        $formula = self::where('slug', $slug)->first();

        if (!$formula) {
            throw new Exception("Payroll formula '{$slug}' not found.");
        }

        return $formula->minimum_amount;
    }

}
