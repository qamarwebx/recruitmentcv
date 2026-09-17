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
            $table->string('status')->nullable()->after('lead_type');
            $table->string('source_id')->nullable()->after('status');
            $table->string('has_email')->nullable()->after('source_id');
            $table->string('has_mobile')->nullable()->after('has_email');
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
            $table->dropColumn(['status', 'source_id', 'has_email', 'has_mobile']);
        });
    }
};
