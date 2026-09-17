<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('all_contact_export_history_admin_filters', function (Blueprint $table) {
            $table->dropColumn(['filter_admin_id', 'created_from', 'created_to']);
        });

        Schema::table('all_contact_export_history_admin_filters', function (Blueprint $table) {
            $table->string('filter_admin_id')->nullable()->after('status');
            $table->string('created_date')->nullable()->after('business_type');
        });
    }

    public function down(): void
    {
        Schema::table('all_contact_export_history_admin_filters', function (Blueprint $table) {
            $table->dropColumn(['filter_admin_id', 'created_date']);
        });

        Schema::table('all_contact_export_history_admin_filters', function (Blueprint $table) {
            $table->unsignedBigInteger('filter_admin_id')->nullable()->after('status');
            $table->date('created_from')->nullable()->after('business_type');
            $table->date('created_to')->nullable()->after('created_from');
        });
    }
};
