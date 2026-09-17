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
        Schema::table('autometanotifications', function (Blueprint $table) {
            $table->enum('action_type', ['immediate','wait'])
                  ->default('immediate')
                  ->after('trigger_template_type');
        });
    }
    
   

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('autometanotifications', function (Blueprint $table) {
            $table->dropColumn('action_type');
        });
    }
};
