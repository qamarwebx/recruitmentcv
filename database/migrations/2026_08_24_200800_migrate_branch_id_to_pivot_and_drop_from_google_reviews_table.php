<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Preserve any existing single branch_id assignment in the new
        // many-to-many pivot before the column is dropped.
        DB::table('google_reviews')->whereNotNull('branch_id')->orderBy('id')->get()->each(function ($review) {
            DB::table('google_review_branch')->insertOrIgnore([
                'google_review_id' => $review->id,
                'branch_id' => $review->branch_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        Schema::table('google_reviews', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn('branch_id');
        });
    }

    public function down(): void
    {
        Schema::table('google_reviews', function (Blueprint $table) {
            $table->unsignedBigInteger('branch_id')->nullable()->after('id');
        });

        DB::table('google_review_branch')->orderBy('id')->get()->each(function ($pivot) {
            DB::table('google_reviews')->where('id', $pivot->google_review_id)->update([
                'branch_id' => $pivot->branch_id,
            ]);
        });

        Schema::table('google_reviews', function (Blueprint $table) {
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('restrict');
        });
    }
};
