<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('google_review_admin_save_filters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id')->unique();
            $table->string('by_branch')->nullable();
            $table->string('by_created_by')->nullable();
            $table->string('by_date_range')->nullable();
            $table->string('by_approval_status')->nullable();
            $table->string('by_payment_status')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('google_review_admin_save_filters');
    }
};
