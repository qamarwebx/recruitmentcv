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
        Schema::table('allcontactadminsavefilters', function (Blueprint $table) {
            $table->string('conversation_type')->nullable()->after('lead_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('allcontactadminsavefilters', function (Blueprint $table) {
            $table->dropColumn('conversation_type');
        });
    }
};
