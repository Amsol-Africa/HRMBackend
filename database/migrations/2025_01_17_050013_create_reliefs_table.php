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
        Schema::create('reliefs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->onDelete('cascade')->nullable();
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('tax_application', ['before_tax', 'after_tax']);
            $table->enum('relief_type', ['rate', 'fixed']);
            $table->enum('comparison_method', ['least', 'greatest'])->nullable();
            $table->decimal('rate_percentage', 8, 2)->nullable();
            $table->decimal('fixed_amount', 15, 2)->nullable();
            $table->decimal('maximum_relief', 15, 2)->nullable();
            $table->boolean('is_mandatory')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reliefs');
    }
};
