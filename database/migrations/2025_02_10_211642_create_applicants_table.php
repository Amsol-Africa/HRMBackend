<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('applicants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('country')->nullable();
            $table->string('linkedin_profile')->nullable();
            $table->string('portfolio_url')->nullable();
            $table->text('summary')->nullable(); //about me section
            $table->string('current_job_title')->nullable();
            $table->string('current_company')->nullable();
            $table->string('experience_level')->nullable(); // (e.g., Entry-level, Mid-level, Senior)
            $table->string('education_level')->nullable(); // (e.g., High School, Bachelor's, Master's, PhD)
            $table->string('desired_salary')->nullable();
            $table->string('job_preferences')->nullable(); // (e.g., types of roles, industries)
            $table->string('source')->nullable(); // How did the applicant hear about the company? (e.g., LinkedIn, Indeed, Referral)
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applicants');
    }
};
