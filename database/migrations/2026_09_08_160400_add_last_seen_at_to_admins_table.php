<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('admins', function (Blueprint $table) {
            // Lightweight chat presence heartbeat - last_login_at only fires at
            // login, it doesn't track ongoing activity.
            $table->timestamp('last_seen_at')->nullable()->after('last_login_at');
        });
    }

    public function down()
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn('last_seen_at');
        });
    }
};
