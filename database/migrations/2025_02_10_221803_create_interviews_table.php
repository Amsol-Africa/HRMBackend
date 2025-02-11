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
        Schema::create('interviews', function (Blueprint $table) { //use recruitment stages as status
            $table->id();
            $table->foreignId('application_id')->constrained()->onDelete('cascade');
            $table->foreignId('interviewer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('type', ['phone', 'video', 'in-person'])->default('in-person');
            $table->string('location')->nullable(); // Only required for physical interviews
            $table->string('meeting_link')->nullable(); // Only required for virtual interviews
            $table->timestamp('scheduled_at');
            $table->text('notes')->nullable();
            // $table->enum('status', ['scheduled', 'completed', 'canceled'])->default('scheduled');
            $table->string('outcome')->nullable(); // (e.g., Positive, Negative, Neutral, Hired, Rejected)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interviews');
    }
};
