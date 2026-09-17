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
            // Add missing column first if not exists
            if (!Schema::hasColumn('autometanotifications', 'metatemp_id')) {
                $table->integer('metatemp_id')->nullable()->after('id');
            }

            // Make these columns nullable
            $table->string('template_name', 255)->nullable()->change();
            $table->string('meta_template_name', 255)->nullable()->change();
            $table->text('meta_message_body')->nullable()->change();
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
            if (Schema::hasColumn('autometanotifications', 'metatemp_id')) {
                $table->dropColumn('metatemp_id');
            }

            // revert to NOT NULL (if needed)
            $table->string('template_name', 255)->nullable(false)->change();
            $table->string('meta_template_name', 255)->nullable(false)->change();
            $table->text('meta_message_body')->nullable(false)->change();
        });
    }
};
