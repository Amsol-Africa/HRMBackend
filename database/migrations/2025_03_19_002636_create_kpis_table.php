<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKpisTable extends Migration
{
    public function up()
    {
        Schema::create('kpis', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Employee Attendance Rate"
            $table->string('slug')->unique(); // e.g., "employee-attendance-rate"
            $table->string('model_type'); // e.g., "App\Models\Attendance"
            $table->text('description')->nullable(); // Description of the KPI
            $table->string('calculation_method'); // e.g., "percentage", "count", "average"
            $table->string('target_value'); // e.g., "95" (for 95% attendance)
            $table->string('comparison_operator'); // e.g., ">=", "<=", "="
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('kpis');
    }
}