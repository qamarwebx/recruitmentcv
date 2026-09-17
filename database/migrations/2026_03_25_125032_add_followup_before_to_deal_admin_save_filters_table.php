<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('deal_admin_save_filters', function (Blueprint $table) {
            $table->string('followup_before')->nullable()->after('updated_date');
        });
    }

    public function down()
    {
        Schema::table('deal_admin_save_filters', function (Blueprint $table) {
            $table->dropColumn('followup_before');
        });
    }
};
