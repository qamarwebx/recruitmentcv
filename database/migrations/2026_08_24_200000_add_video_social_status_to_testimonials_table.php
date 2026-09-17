<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('testimonials', function (Blueprint $table) {
            $table->string('video_received_status')->default('Not Received')->after('paid_at');
            $table->string('social_media_status')->default('Not Posted')->after('video_received_status');
            $table->text('social_media_platforms')->nullable()->after('social_media_status');
        });
    }

    public function down(): void
    {
        Schema::table('testimonials', function (Blueprint $table) {
            $table->dropColumn(['video_received_status', 'social_media_status', 'social_media_platforms']);
        });
    }
};
