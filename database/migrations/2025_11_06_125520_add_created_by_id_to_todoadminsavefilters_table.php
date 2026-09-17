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
        Schema::table('todoadminsavefilters', function (Blueprint $table) {
            $table->text('created_by_id')->nullable()->after('assignto_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('todoadminsavefilters', function (Blueprint $table) {
            $table->dropColumn('created_by_id');

        });
    }
};
