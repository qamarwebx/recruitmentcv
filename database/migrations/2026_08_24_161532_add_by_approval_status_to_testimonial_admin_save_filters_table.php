<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('testimonial_admin_save_filters', function (Blueprint $table) {
            $table->string('by_approval_status')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('testimonial_admin_save_filters', function (Blueprint $table) {
            $table->dropColumn('by_approval_status');
        });
    }
};
