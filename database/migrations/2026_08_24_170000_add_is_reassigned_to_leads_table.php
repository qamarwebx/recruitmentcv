<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->boolean('is_reassigned')
                  ->default(false)
                  ->after('leadassign_id')
                  ->comment('True once a lead already assigned to someone is reassigned to a different admin via the Assign Lead modal');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn('is_reassigned');
        });
    }
};
