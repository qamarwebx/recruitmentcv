<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Additive only: which partner website a customer (users row) belongs to.
 * NULL = the main recruitmentcv.com site (all existing rows stay NULL).
 * nullOnDelete so the CRM's existing partner delete keeps working.
 */
return new class extends Migration
{
    public function up()
    {
        if (Schema::hasColumn('users', 'partner_id')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('partner_id')->nullable()->after('company_name');
            $table->foreign('partner_id')->references('id')->on('partners')->nullOnDelete();
        });
    }

    public function down()
    {
        if (!Schema::hasColumn('users', 'partner_id')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['partner_id']);
            $table->dropColumn('partner_id');
        });
    }
};
