<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('google_review_approval_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('google_review_id');
            $table->enum('action', ['approved', 'rejected']);
            $table->string('previous_status')->nullable();
            $table->string('new_status');
            $table->unsignedBigInteger('admin_id');
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('google_review_id')->references('id')->on('google_reviews')->onDelete('cascade');
            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade');
            $table->index(['google_review_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('google_review_approval_logs');
    }
};
