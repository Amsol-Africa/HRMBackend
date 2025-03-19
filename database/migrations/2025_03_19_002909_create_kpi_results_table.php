<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKpiResultsTable extends Migration
{
    public function up()
    {
        Schema::create('kpi_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kpi_id')->constrained()->onDelete('cascade');
            $table->string('model_type'); // e.g., "App\Models\Attendance"
            $table->unsignedBigInteger('model_id'); // e.g., attendance record ID
            $table->decimal('result_value', 10, 2); // Calculated KPI value
            $table->boolean('meets_target')->default(false); // Did it meet the target?
            $table->date('measured_at'); // Date of measurement
            $table->timestamps();

            $table->index(['model_type', 'model_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('kpi_results');
    }
}