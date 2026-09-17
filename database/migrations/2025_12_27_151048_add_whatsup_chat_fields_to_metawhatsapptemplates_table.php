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
        Schema::table('metawhatsapptemplates', function (Blueprint $table) {

                $table->text('whatsup_chat_careoff_id_static')
                ->nullable()
                ->after('careoff_field_static');

                $table->text('whatsup_chat_url_static')
                ->nullable()
                ->after('whatsup_chat_careoff_id_static');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('metawhatsapptemplates', function (Blueprint $table) {

            $table->dropColumn([
                'whatsup_chat_careoff_id_static',
                'whatsup_chat_url_static'
            ]);

        });
    }
};
