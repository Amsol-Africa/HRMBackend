<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->string('registration_no')->nullable()->after('code');
            $table->string('tax_pin_no')->nullable()->after('registration_no');
            $table->string('business_license_no')->nullable()->after('tax_pin_no');
            $table->string('physical_address')->nullable()->after('business_license_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn([
                'registration_no',
                'tax_pin_no',
                'business_license_no',
                'physical_address',
            ]);
        });
    }
};
