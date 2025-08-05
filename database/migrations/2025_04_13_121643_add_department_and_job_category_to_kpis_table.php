<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDepartmentAndJobCategoryToKpisTable extends Migration
{
    public function up()
    {
        Schema::table('kpis', function (Blueprint $table) {
            $table->unsignedBigInteger('department_id')->nullable()->after('employee_id');
            $table->unsignedBigInteger('job_category_id')->nullable()->after('department_id');

            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
            $table->foreign('job_category_id')->references('id')->on('job_categories')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('kpis', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropForeign(['job_category_id']);
            $table->dropColumn(['department_id', 'job_category_id']);
        });
    }
}
