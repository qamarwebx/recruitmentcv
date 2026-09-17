<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('deal_reminders', function (Blueprint $table) {
            $table->text('whatsapp_description')->nullable()->after('reminder_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('deal_reminders', function (Blueprint $table) {
            $table->dropColumn('whatsapp_description');
        });
    }
};
