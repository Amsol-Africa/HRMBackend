<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('employee_payrolls', function (Blueprint $table) {
            $table->json('deductions')->nullable()->after('net_pay');
            $table->json('overtime')->nullable()->after('deductions');
        });
    }

    public function down()
    {
        Schema::table('employee_payrolls', function (Blueprint $table) {
            $table->dropColumn('overtime');
            $table->dropColumn('deductions');
        });
    }
};
