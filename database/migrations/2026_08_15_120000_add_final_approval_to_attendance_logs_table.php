<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendance_logs', function (Blueprint $table) {
            // Phase 2 of attendance approval - only takes effect once Normal
            // Approval (the existing `status` column) is 'approved'.
            $table->enum('final_status', ['pending', 'approved', 'rejected'])->default('pending')->after('rejection_reason');
            $table->unsignedBigInteger('final_approved_by')->nullable()->after('final_status');
            $table->dateTime('final_approved_at')->nullable()->after('final_approved_by');
            $table->text('final_rejection_reason')->nullable()->after('final_approved_at');

            $table->foreign('final_approved_by')->references('id')->on('admins')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->dropForeign(['final_approved_by']);
            $table->dropColumn([
                'final_status',
                'final_approved_by',
                'final_approved_at',
                'final_rejection_reason',
            ]);
        });
    }
};
