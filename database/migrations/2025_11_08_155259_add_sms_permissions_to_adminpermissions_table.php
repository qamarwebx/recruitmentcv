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
        Schema::table('adminpermissions', function (Blueprint $table) {
            // SMS Campaign Module Access
            $table->boolean('sms_campaign_module')->default(false)->after('access_allowed_ip_delete');
            // SMS API Permissions
            $table->boolean('sms_api')->default(false)->after('sms_campaign_module');
            $table->boolean('add_sms_api')->default(false)->after('sms_api');
            $table->boolean('edit_sms_api')->default(false)->after('add_sms_api');
            $table->boolean('delete_sms_api')->default(false)->after('edit_sms_api');
            $table->boolean('change_status_sms_api')->default(false)->after('delete_sms_api');
            $table->boolean('assign_sms_api')->default(false)->after('change_status_sms_api');

            // SMS Template Permissions
            $table->boolean('sms_template')->default(false)->after('assign_sms_api');
            $table->boolean('add_sms_template')->default(false)->after('sms_template');
            $table->boolean('edit_sms_template')->default(false)->after('add_sms_template');
            $table->boolean('delete_sms_template')->default(false)->after('edit_sms_template');
            $table->boolean('change_status_sms_template')->default(false)->after('delete_sms_template');

            // SMS Campaign Permissions
            $table->boolean('sms_campaign')->default(false)->after('change_status_sms_template');
            $table->boolean('add_sms_campaign')->default(false)->after('sms_campaign');
            $table->boolean('view_sms_campaign')->default(false)->after('add_sms_campaign');
            $table->boolean('delete_sms_campaign')->default(false)->after('view_sms_campaign');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('adminpermissions', function (Blueprint $table) {
            $table->dropColumn([
                'sms_campaign_module',
                'sms_api',
                'add_sms_api',
                'edit_sms_api',
                'delete_sms_api',
                'change_status_sms_api',
                'assign_sms_api',
                'sms_template',
                'add_sms_template',
                'edit_sms_template',
                'delete_sms_template',
                'change_status_sms_template',
                'sms_campaign',
                'add_sms_campaign',
                'view_sms_campaign',
                'delete_sms_campaign',
            ]);
        });
    }
};
