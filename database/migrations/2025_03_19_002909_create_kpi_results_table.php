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
            $table->unsignedBigInteger('kpi_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->decimal('result_value', 15, 2)->nullable();
            $table->boolean('meets_target')->default(false);
            $table->date('measured_at');
            $table->timestamps();

            $table->foreign('kpi_id')->references('id')->on('kpis')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('kpi_results');
    }
}
