<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('export_all_contact_email_portal_histories', function (Blueprint $table) {
            $table->timestamp('last_refreshed_at')->nullable()->after('completed_at');
        });
    }

    public function down(): void
    {
        Schema::table('export_all_contact_email_portal_histories', function (Blueprint $table) {
            $table->dropColumn('last_refreshed_at');
        });
    }
};
