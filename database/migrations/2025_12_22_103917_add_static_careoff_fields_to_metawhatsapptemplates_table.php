<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('metawhatsapptemplates', function (Blueprint $table) {

            $table->string('careoff_id_static', 255)
                  ->nullable()
                  ->after('meta_assign_ar'); // adjust position if needed

            $table->string('careoff_field_static', 255)
                  ->nullable()
                  ->after('careoff_id_static');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('metawhatsapptemplates', function (Blueprint $table) {

            $table->dropColumn([
                'careoff_id_static',
                'careoff_field_static',
            ]);
        });
    }
};
