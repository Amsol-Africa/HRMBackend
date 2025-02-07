<?php

namespace App\Models;

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
        return SlugOptions::create()->generateSlugsFrom('name')->saveSlugsTo('slug');
    }
    public static function calculate($slug, $amount)
    {
        // Fetch formula by slug (e.g., 'paye', 'nhif', 'nssf', 'housing-levy')
        $formula = self::where('slug', $slug)->first();

        if (!$formula) {
            throw new \Exception("Payroll formula '{$slug}' not found.");
        }

        // Determine calculation type (Flat Rate or Progressive)
        if ($formula->is_progressive) {
            return self::calculateProgressive($amount, $formula->brackets);
        }

        return self::calculateFlat($amount, $formula);
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
            throw new \Exception("Payroll formula '{$slug}' not found.");
        }

        return $formula->minimum_amount;
    }

}
