<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('adminpermissions', function (Blueprint $table) {
            $table->boolean('video_received_testimonial')->default(false);
            $table->boolean('social_media_testimonial')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('adminpermissions', function (Blueprint $table) {
            $table->dropColumn(['video_received_testimonial', 'social_media_testimonial']);
        });
    }
};
