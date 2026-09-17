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
            $table->string('by_staff_updated_date')->nullable()->after('by_updated_date');
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
            $table->dropColumn('by_staff_updated_date');
        });
    }
};
