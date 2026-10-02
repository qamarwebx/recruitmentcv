<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Additive only: the Partner's Recruitment Licence Number (Partner Account),
 * next to the office names on the same partners row. NULL = not provided;
 * no existing row or column is changed.
 */
return new class extends Migration
{
    public function up()
    {
        if (Schema::hasColumn('partners', 'licence_number')) {
            return;
        }

        Schema::table('partners', function (Blueprint $table) {
            $table->string('licence_number', 100)->nullable()->after('rec_office_arname');
        });
    }

    public function down()
    {
        if (Schema::hasColumn('partners', 'licence_number')) {
            Schema::table('partners', function (Blueprint $table) {
                $table->dropColumn('licence_number');
            });
        }
    }
};
