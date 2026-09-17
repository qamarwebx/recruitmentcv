<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('candidateadminsavefilters', function (Blueprint $table) {
            $table->text('exp_sal')->nullable()->after('careoff_id');
        });
    }

    public function down(): void
    {
        Schema::table('candidateadminsavefilters', function (Blueprint $table) {
            $table->dropColumn('exp_sal');
        });
    }
};