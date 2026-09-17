<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contactplusadminsavefilters', function (Blueprint $table) {
            $table->tinyInteger('short_form_code')->default(0)->nullable(false)->after('subscribe');
            $table->string('last_business_type_id')->nullable()->after('short_form_code');
            $table->string('last_country_id')->nullable()->after('last_business_type_id');
        });
    }

    public function down(): void
    {
        Schema::table('contactplusadminsavefilters', function (Blueprint $table) {
            $table->dropColumn('short_form_code');
            $table->dropColumn('last_business_type_id');
            $table->dropColumn('last_country_id');
        });
    }
};
