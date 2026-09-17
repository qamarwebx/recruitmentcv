<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('full_name')->nullable();
            $table->string('passport_number')->nullable();
            $table->string('uploaded_video')->nullable(); // path to uploaded video
            $table->unsignedInteger('uploading_video_max_limit')->default(0); // size in MB
            $table->string('link')->nullable(); // external video link (YouTube, etc.)
            $table->unsignedInteger('created_by');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};
