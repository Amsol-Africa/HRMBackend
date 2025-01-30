<?php

use App\Models\Business;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payroll_formulas', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Business::class)->nullable();
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('calculation_basis', ['basic pay', 'gross pay', 'cash pay', 'taxable pay']);
            $table->boolean('is_progressive')->default(false);
            $table->decimal('minimum_amount', 15, 2)->nullable();
            $table->json('brackets')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_formulas');
    }
};
