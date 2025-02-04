<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PayrollFormula;
use App\Models\PayrollFormulaBracket;

class PayrollFormulaSeeder extends Seeder
{
    public function run()
    {
        // PAYE Tax Brackets
        $paye = PayrollFormula::create([
            'name' => 'PAYE',
            'formula_type' => 'rate',
            'calculation_basis' => 'taxable_pay',
            'is_progressive' => true,
        ]);

        PayrollFormulaBracket::insert([
            ['payroll_formula_id' => $paye->id, 'min' => 0, 'max' => 24000, 'rate' => 10.0],
            ['payroll_formula_id' => $paye->id, 'min' => 24001, 'max' => 32333, 'rate' => 25.0],
            ['payroll_formula_id' => $paye->id, 'min' => 32334, 'max' => 500000, 'rate' => 30.0],
            ['payroll_formula_id' => $paye->id, 'min' => 500001, 'max' => null, 'rate' => 35.0],
        ]);

        // NHIF Brackets
        $nhif = PayrollFormula::create([
            'name' => 'NHIF',
            'formula_type' => 'amount',
            'calculation_basis' => 'gross_pay',
            'is_progressive' => true,
        ]);

        PayrollFormulaBracket::insert([
            ['payroll_formula_id' => $nhif->id, 'min' => 0, 'max' => 5999, 'amount' => 150],
            ['payroll_formula_id' => $nhif->id, 'min' => 6000, 'max' => 7999, 'amount' => 300],
            ['payroll_formula_id' => $nhif->id, 'min' => 8000, 'max' => 11999, 'amount' => 400],
            ['payroll_formula_id' => $nhif->id, 'min' => 12000, 'max' => 14999, 'amount' => 500],
            ['payroll_formula_id' => $nhif->id, 'min' => 15000, 'max' => 19999, 'amount' => 600],
            ['payroll_formula_id' => $nhif->id, 'min' => 20000, 'max' => 24999, 'amount' => 750],
            ['payroll_formula_id' => $nhif->id, 'min' => 25000, 'max' => null, 'amount' => 1700],
        ]);

        // Housing Levy
        PayrollFormula::create([
            'name' => 'Housing Levy',
            'formula_type' => 'rate',
            'calculation_basis' => 'gross_pay',
            'is_progressive' => false,
            'minimum_amount' => 0.015, // 1.5% Housing Levy
        ]);

        // Personal Relief
        PayrollFormula::create([
            'name' => 'Personal Relief',
            'formula_type' => 'fixed',
            'calculation_basis' => 'taxable_pay',
            'is_progressive' => false,
            'minimum_amount' => 2400,
        ]);
    }
}
