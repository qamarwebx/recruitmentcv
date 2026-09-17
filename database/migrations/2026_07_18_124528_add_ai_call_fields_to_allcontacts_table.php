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
         Schema::table('allcontacts', function (Blueprint $table) {
            $table->string('ai_all_status')->default('pending')->after('primary_no_wsp');
            $table->longText('ai_call_response')->nullable()->after('ai_all_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('allcontacts', function (Blueprint $table) {
            $table->dropColumn([
                'ai_call_status',
                'ai_call_response',
            ]);
        });
    }
};
