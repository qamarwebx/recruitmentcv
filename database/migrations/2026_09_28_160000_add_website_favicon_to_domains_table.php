<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Additive only: partner website favicon, stored next to the existing
 * website_logo / website_logo_ar on the same domains row (same files folder,
 * admin/assets/images/partner/). NULL = use the default RecruitmentCV favicon.
 */
return new class extends Migration
{
    public function up()
    {
        if (Schema::hasColumn('domains', 'website_favicon')) {
            return;
        }

        Schema::table('domains', function (Blueprint $table) {
            $table->string('website_favicon')->nullable()->after('website_logo_ar');
        });
    }

    public function down()
    {
        if (Schema::hasColumn('domains', 'website_favicon')) {
            Schema::table('domains', function (Blueprint $table) {
                $table->dropColumn('website_favicon');
            });
        }
    }
};
