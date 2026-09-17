<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTriggerFieldsToAutometanotificationsTable extends Migration
{
    public function up()
    {
        Schema::table('autometanotifications', function (Blueprint $table) {
            $table->string('trigger_template_type')->nullable()->after('meta_message_body');
            $table->string('trigger_template_time')->nullable()->after('trigger_template_type');
            $table->string('trigger_template_time_type')->nullable()->after('trigger_template_time');
            $table->string('template_table_name')->nullable()->after('trigger_template_time_type');
            $table->text('field_not_completed')->nullable()->after('template_table_name'); 
        });
    }

    public function down()
    {
        Schema::table('autometanotifications', function (Blueprint $table) {
            $table->dropColumn('trigger_template_type');
            $table->dropColumn('trigger_template_time');
            $table->dropColumn('trigger_template_time_type');
            $table->dropColumn(['template_table_name', 'field_not_completed']);

        });
    }
}
