<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('allcontactadminsavefilters', function (Blueprint $table) {
            $table->text('exlude_country_mobile_code')
                  ->nullable()
                  ->after('country_dial_code_number');
        });
    }

    public function down(): void
    {
        Schema::table('allcontactadminsavefilters', function (Blueprint $table) {
            $table->dropColumn('exlude_country_mobile_code');
        });
    }
};
