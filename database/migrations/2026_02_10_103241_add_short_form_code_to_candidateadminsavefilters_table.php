<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('candidateadminsavefilters', function (Blueprint $table) {
            $table->tinyInteger('short_form_code')
                  ->default(0)
                  ->nullable(false)
                  ->after('admin_id');
        });
    }

    public function down(): void
    {
        Schema::table('candidateadminsavefilters', function (Blueprint $table) {
            $table->dropColumn('short_form_code');
        });
    }
};
