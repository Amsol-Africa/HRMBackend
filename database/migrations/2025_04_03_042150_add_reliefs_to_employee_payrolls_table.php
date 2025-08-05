<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReliefsToEmployeePayrollsTable extends Migration
{
    public function up()
    {
        Schema::table('employee_payrolls', function (Blueprint $table) {
            $table->json('reliefs')->nullable()->after('taxable_income');
        });
    }

    public function down()
    {
        Schema::table('employee_payrolls', function (Blueprint $table) {
            $table->dropColumn('reliefs');
        });
    }
}