<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('testimonial_admin_save_filters', function (Blueprint $table) {
            $table->id();

            // Each admin has only one saved filter for testimonials
            $table->unsignedBigInteger('admin_id')->unique();

            $table->string('by_full_name')->nullable();
            $table->string('by_passport_number')->nullable();
            $table->string('by_created_by')->nullable(); // selected care of / created by staff (comma-separated)
            $table->string('by_date_range')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonial_admin_save_filters');
    }
};
