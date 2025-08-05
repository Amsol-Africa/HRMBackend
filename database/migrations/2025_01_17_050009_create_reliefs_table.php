<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReliefsTable extends Migration
{
    public function up()
    {
        Schema::create('reliefs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('type')->default('deductible_after_tax');
            $table->foreignId('location_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('greatest_or_least_of')->default('least');
            $table->decimal('amount', 10, 2);
            $table->boolean('actual_amount')->default(false);
            $table->decimal('percentage_of_amount', 5, 2)->nullable();
            $table->string('percentage_of')->nullable();
            $table->string('fraction_to_consider')->default('employee_only');
            $table->decimal('limit', 10, 2)->nullable();
            $table->string('round_off')->default('round_off_up');
            $table->tinyInteger('decimal_places')->default(2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reliefs');
    }
}