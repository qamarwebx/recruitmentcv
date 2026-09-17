<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::table('adminpermissions', function (Blueprint $table) {

            $table->boolean('whatsapp_url')
                  ->default(false)
                  ->after('id');

            $table->boolean('add_whatsapp_url')
                  ->default(false)
                  ->after('whatsapp_url');

            $table->boolean('edit_whatsapp_url')
                  ->default(false)
                  ->after('add_whatsapp_url');

            $table->boolean('view_whatsapp_url')
                  ->default(false)
                  ->after('edit_whatsapp_url');

            $table->boolean('delete_whatsapp_url')
                  ->default(false)
                  ->after('view_whatsapp_url');
        });
    }

    public function down(): void
    {
        Schema::table('adminpermissions', function (Blueprint $table) {

            $table->dropColumn([
                'whatsapp_url',
                'add_whatsapp_url',
                'edit_whatsapp_url',
                'view_whatsapp_url',
                'delete_whatsapp_url',
            ]);
        });
    }
};
