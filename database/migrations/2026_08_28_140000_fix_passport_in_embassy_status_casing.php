<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('candidate_statuses')
            ->where('status', 'Passport in Embassy')
            ->update(['status' => 'Passport In Embassy']);
    }

    public function down(): void
    {
        DB::table('candidate_statuses')
            ->where('status', 'Passport In Embassy')
            ->update(['status' => 'Passport in Embassy']);
    }
};
