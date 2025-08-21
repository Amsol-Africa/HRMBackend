<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify the ENUM to include 'custom'
        DB::statement("ALTER TABLE deductions MODIFY COLUMN calculation_basis ENUM('basic_pay', 'gross_pay', 'cash_pay', 'taxable_pay', 'custom') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to the original ENUM values
        DB::statement("ALTER TABLE deductions MODIFY COLUMN calculation_basis ENUM('basic_pay', 'gross_pay', 'cash_pay', 'taxable_pay') NOT NULL");
    }
};
