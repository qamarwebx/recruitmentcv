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
            $table->boolean('meta_whatsapp_template')->default(false);
            $table->boolean('add_meta_whatsapp_template')->default(false);
            $table->boolean('edit_meta_whatsapp_template')->default(false);
            $table->boolean('delete_meta_whatsapp_template')->default(false);
            $table->boolean('status_meta_whatsapp_template')->default(false);
            $table->boolean('public_meta_whatsapp_template')->default(false);
            $table->boolean('meta_whatsapp_campaign')->default(false);
            $table->boolean('meta_whatsapp_campaign_view')->default(false);
            $table->boolean('whatsapp_nromal')->default(false);
            $table->boolean('whatsapp_template')->default(false);
            $table->boolean('add_whatsapp_template')->default(false);
            $table->boolean('edit_whatsapp_template')->default(false);
            $table->boolean('delete_whatsapp_template')->default(false);
            $table->boolean('status_whatsapp_template')->default(false);
            $table->boolean('public_whatsapp_template')->default(false);
            $table->boolean('whatsapp_campaign')->default(false);
            $table->boolean('whatsapp_campaign_view')->default(false);

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
            $table->dropColumn('meta_whatsapp_template');
            $table->dropColumn('add_meta_whatsapp_template');
            $table->dropColumn('edit_meta_whatsapp_template');
            $table->dropColumn('delete_meta_whatsapp_template');
            $table->dropColumn('status_meta_whatsapp_template');
            $table->dropColumn('public_meta_whatsapp_template');
            $table->dropColumn('meta_whatsapp_campaign');
            $table->dropColumn('meta_whatsapp_campaign_view');
            $table->dropColumn('whatsapp_nromal');
            $table->dropColumn('whatsapp_template');
            $table->dropColumn('add_whatsapp_template');
            $table->dropColumn('edit_whatsapp_template');
            $table->dropColumn('delete_whatsapp_template');
            $table->dropColumn('status_whatsapp_template');
            $table->dropColumn('public_whatsapp_template');
            $table->dropColumn('whatsapp_campaign');
            $table->dropColumn('whatsapp_campaign_view');
        });
    }
};
