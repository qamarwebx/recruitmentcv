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
            $table->tinyInteger('short_form_code')->default(0)->nullable(false)->after('admin_id');
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
            $table->dropColumn('short_form_code');
        });
    }
};
