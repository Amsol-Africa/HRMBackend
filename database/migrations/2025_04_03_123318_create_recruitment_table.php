<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecruitmentTables extends Migration
{
    public function up()
    {
        // Update job_posts table
        Schema::table('job_posts', function (Blueprint $table) {
            $table->string('status')->default('draft'); // draft, published, closed
            $table->text('description')->nullable();
            $table->date('closing_date')->nullable();
            $table->boolean('is_public')->default(false);
        });

        // Update applications table
        Schema::table('applications', function (Blueprint $table) {
            $table->string('stage')->default('applied'); // applied, shortlisted, interviewed, offered, rejected
            $table->text('notes')->nullable();
        });

        // Update applicants table
        Schema::table('applicants', function (Blueprint $table) {
            $table->string('phone')->nullable();
            $table->text('resume_path')->nullable();
            $table->text('cover_letter')->nullable();
        });

        // Update interviews table
        Schema::table('interviews', function (Blueprint $table) {
            $table->string('location')->nullable();
            $table->string('type')->default('in-person'); // in-person, virtual
            $table->boolean('notified')->default(false);
        });

        // Create psychometric_tests table
        Schema::create('psychometric_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->constrained()->onDelete('cascade');
            $table->foreignId('business_id')->constrained()->onDelete('cascade');
            $table->string('test_type'); // e.g., aptitude, personality
            $table->json('results')->nullable();
            $table->integer('score')->nullable();
            $table->string('status')->default('pending'); // pending, completed
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('psychometric_tests');
        Schema::table('job_posts', function (Blueprint $table) {
            $table->dropColumn(['status', 'description', 'closing_date', 'is_public']);
        });
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn(['stage', 'notes']);
        });
        Schema::table('applicants', function (Blueprint $table) {
            $table->dropColumn(['phone', 'resume_path', 'cover_letter']);
        });
        Schema::table('interviews', function (Blueprint $table) {
            $table->dropColumn(['location', 'type', 'notified']);
        });
    }
}