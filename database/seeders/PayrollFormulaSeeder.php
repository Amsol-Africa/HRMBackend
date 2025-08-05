<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PayrollFormulaSeeder extends Seeder
{
    public function run()
    {
        DB::transaction(function () {
            // Disable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // Truncate both tables
            DB::table('payroll_formula_brackets')->truncate();
            DB::table('payroll_formulas')->truncate();

            // 1. PAYE
            $payeId = DB::table('payroll_formulas')->insertGetId([
                'name' => 'PAYE',
                'slug' => 'paye',
                'formula_type' => 'rate',
                'calculation_basis' => 'taxable_pay',
                'is_progressive' => 1,
                'minimum_amount' => null,
                'applies_to' => 'all',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('payroll_formula_brackets')->insert([
                ['payroll_formula_id' => $payeId, 'min' => 0, 'max' => 24000, 'rate' => 10.00, 'amount' => null, 'created_at' => now(), 'updated_at' => now()],
                ['payroll_formula_id' => $payeId, 'min' => 24001, 'max' => 32333, 'rate' => 25.00, 'amount' => null, 'created_at' => now(), 'updated_at' => now()],
                ['payroll_formula_id' => $payeId, 'min' => 32334, 'max' => 500000, 'rate' => 30.00, 'amount' => null, 'created_at' => now(), 'updated_at' => now()],
                ['payroll_formula_id' => $payeId, 'min' => 500001, 'max' => null, 'rate' => 35.00, 'amount' => null, 'created_at' => now(), 'updated_at' => now()],
            ]);

            // 2. SHIF
            $shifId = DB::table('payroll_formulas')->insertGetId([
                'name' => 'SHIF',
                'slug' => 'shif',
                'formula_type' => 'amount',
                'calculation_basis' => 'gross_pay',
                'is_progressive' => 1,
                'minimum_amount' => null,
                'applies_to' => 'all',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('payroll_formula_brackets')->insert([
                ['payroll_formula_id' => $shifId, 'min' => 0, 'max' => 5999, 'rate' => null, 'amount' => 150, 'created_at' => now(), 'updated_at' => now()],
                ['payroll_formula_id' => $shifId, 'min' => 6000, 'max' => 7999, 'rate' => null, 'amount' => 300, 'created_at' => now(), 'updated_at' => now()],
                ['payroll_formula_id' => $shifId, 'min' => 8000, 'max' => 11999, 'rate' => null, 'amount' => 400, 'created_at' => now(), 'updated_at' => now()],
                ['payroll_formula_id' => $shifId, 'min' => 12000, 'max' => 14999, 'rate' => null, 'amount' => 500, 'created_at' => now(), 'updated_at' => now()],
                ['payroll_formula_id' => $shifId, 'min' => 15000, 'max' => 19999, 'rate' => null, 'amount' => 600, 'created_at' => now(), 'updated_at' => now()],
                ['payroll_formula_id' => $shifId, 'min' => 20000, 'max' => 24999, 'rate' => null, 'amount' => 750, 'created_at' => now(), 'updated_at' => now()],
                ['payroll_formula_id' => $shifId, 'min' => 25000, 'max' => null, 'rate' => null, 'amount' => 1700, 'created_at' => now(), 'updated_at' => now()],
            ]);

            // 3. NHDF (Housing Levy)
            $nhdfId = DB::table('payroll_formulas')->insertGetId([
                'name' => 'NHDF',
                'slug' => 'nhdf',
                'formula_type' => 'rate',
                'calculation_basis' => 'gross_pay',
                'is_progressive' => 0,
                'minimum_amount' => 1.50, // 1.5%
                'applies_to' => 'all',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 4. NSSF
            $nssfId = DB::table('payroll_formulas')->insertGetId([
                'name' => 'NSSF',
                'slug' => 'nssf',
                'formula_type' => 'rate',
                'calculation_basis' => 'gross_pay',
                'is_progressive' => 1,
                'minimum_amount' => null,
                'applies_to' => 'all',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('payroll_formula_brackets')->insert([
                ['payroll_formula_id' => $nssfId, 'min' => 0, 'max' => 7000, 'rate' => 6.00, 'amount' => null, 'created_at' => now(), 'updated_at' => now()],
                ['payroll_formula_id' => $nssfId, 'min' => 7001, 'max' => 36000, 'rate' => 6.00, 'amount' => null, 'created_at' => now(), 'updated_at' => now()],
                ['payroll_formula_id' => $nssfId, 'min' => 36001, 'max' => null, 'rate' => null, 'amount' => 2160, 'created_at' => now(), 'updated_at' => now()], // Cap at 2160
            ]);

            // 5. HELB
            $helbId = DB::table('payroll_formulas')->insertGetId([
                'name' => 'HELB',
                'slug' => 'helb',
                'formula_type' => 'rate',
                'calculation_basis' => 'gross_pay',
                'is_progressive' => 0,
                'minimum_amount' => 5.00, // 5%
                'applies_to' => 'specific',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 6. Personal Relief (Fixed)
            $personalReliefId = DB::table('payroll_formulas')->insertGetId([
                'name' => 'Personal Relief',
                'slug' => 'personal-relief',
                'formula_type' => 'fixed',
                'calculation_basis' => 'taxable_pay',
                'is_progressive' => 0,
                'minimum_amount' => 2400, // KES 2,400/month
                'applies_to' => 'all',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        });
    }
}
