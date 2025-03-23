<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateReliefsTable extends Migration
{
    public function up()
    {
        Schema::table('reliefs', function (Blueprint $table) {
            $table->string('type')->after('business_id')->default('deductible_after_tax');
            $table->string('greatest_or_least_of')->after('slug')->default('least');
            $table->boolean('actual_amount')->after('amount')->default(false);
            $table->decimal('percentage_of_amount', 5, 2)->after('actual_amount')->nullable();
            $table->string('percentage_of')->after('percentage_of_amount')->nullable();
            $table->string('fraction_to_consider')->after('percentage_of')->default('employee_only');
            $table->decimal('limit', 10, 2)->after('fraction_to_consider')->nullable();
            $table->string('round_off')->after('limit')->default('round_off_up');
            $table->tinyInteger('decimal_places')->after('round_off')->default(2);
        });
    }

    public function down()
    {
        Schema::table('reliefs', function (Blueprint $table) {
            $table->dropColumn([
                'type',
                'greatest_or_least_of',
                'actual_amount',
                'percentage_of_amount',
                'percentage_of',
                'fraction_to_consider',
                'limit',
                'round_off',
                'decimal_places',
            ]);
        });
    }
}