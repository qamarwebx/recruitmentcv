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
                $table->timestamp('staff_updated_date')
                  ->nullable()
                  ->after('updated_at');
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
            $table->dropColumn('staff_updated_date');
        });
    }
};
