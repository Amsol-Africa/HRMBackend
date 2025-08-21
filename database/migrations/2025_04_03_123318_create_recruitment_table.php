<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Create psychometric_tests table (only if it doesn't exist)
        if (!Schema::hasTable('psychometric_tests')) {
            Schema::create('psychometric_tests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('applicant_id')->constrained()->onDelete('cascade');
                $table->foreignId('business_id')->constrained()->onDelete('cascade');
                $table->string('test_type');
                $table->json('results')->nullable();
                $table->integer('score')->nullable();
                $table->string('status')->default('pending');
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        // Drop psychometric_tests table
        if (Schema::hasTable('psychometric_tests')) {
            Schema::dropIfExists('psychometric_tests');
        }
    }
};
