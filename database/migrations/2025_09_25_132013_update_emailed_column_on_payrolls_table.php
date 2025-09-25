<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->boolean('emailed')->default(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->boolean('emailed')->nullable()->default(null)->change();
        });
    }
};
