<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('metawhatsapptemplates', function (Blueprint $table) {
            $table->string('language_code', 10)
                  ->default('en')
                  ->after('template_name');
        });
    }

    public function down(): void
    {
        Schema::table('metawhatsapptemplates', function (Blueprint $table) {
            $table->dropColumn('language_code');
        });
    }
};
