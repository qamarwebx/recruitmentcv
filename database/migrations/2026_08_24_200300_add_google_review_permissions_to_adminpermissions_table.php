<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('adminpermissions', function (Blueprint $table) {
            $table->boolean('google_review')->default(false);
            $table->boolean('add_google_review')->default(false);
            $table->boolean('edit_google_review')->default(false);
            $table->boolean('view_google_review')->default(false);
            $table->boolean('delete_google_review')->default(false);
            $table->boolean('approval_google_review')->default(false);
            $table->boolean('payment_google_review')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('adminpermissions', function (Blueprint $table) {
            $table->dropColumn([
                'google_review',
                'add_google_review',
                'edit_google_review',
                'view_google_review',
                'delete_google_review',
                'approval_google_review',
                'payment_google_review',
            ]);
        });
    }
};
