<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('testimonial_approval_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('testimonial_id');
            $table->enum('action', ['approved', 'rejected']);
            $table->string('previous_status')->nullable();
            $table->string('new_status');
            $table->unsignedBigInteger('admin_id');
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('testimonial_id')->references('id')->on('testimonials')->onDelete('cascade');
            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade');
            $table->index(['testimonial_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonial_approval_logs');
    }
};
