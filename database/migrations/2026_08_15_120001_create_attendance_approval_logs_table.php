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
        Schema::create('attendance_approval_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attendance_log_id');
            $table->enum('approval_type', ['normal', 'final']);
            $table->enum('action', ['approved', 'rejected']);
            $table->string('previous_status')->nullable();
            $table->string('new_status');
            $table->unsignedBigInteger('admin_id');
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('attendance_log_id')->references('id')->on('attendance_logs')->onDelete('cascade');
            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade');
            $table->index(['attendance_log_id', 'approval_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendance_approval_logs');
    }
};
