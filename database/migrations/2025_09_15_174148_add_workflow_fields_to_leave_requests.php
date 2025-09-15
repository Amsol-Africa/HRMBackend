<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWorkflowFieldsToLeaveRequests extends Migration
{
    public function up()
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->boolean('requires_documentation')->default(false)->after('attachment');
            $table->boolean('is_tentative')->default(false)->after('requires_documentation');
            $table->unsignedTinyInteger('current_approval_level')->default(0)->after('is_tentative');
            $table->json('approval_history')->nullable()->after('current_approval_level');
        });
    }

    public function down()
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropColumn([
                'requires_documentation',
                'is_tentative',
                'current_approval_level',
                'approval_history',
            ]);
        });
    }
}
