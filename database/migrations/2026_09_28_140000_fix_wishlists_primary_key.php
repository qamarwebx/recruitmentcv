<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * The existing `wishlists` table was created without a primary key or
 * AUTO_INCREMENT on `id`, so no row could ever be inserted. Adds them (plus
 * one wishlist entry per customer per candidate). Only runs while the table
 * is still empty and has no primary key - never touches existing data.
 */
return new class extends Migration
{
    public function up()
    {
        $hasPrimary = DB::selectOne(
            "SELECT COUNT(*) AS c FROM information_schema.TABLE_CONSTRAINTS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'wishlists' AND CONSTRAINT_TYPE = 'PRIMARY KEY'"
        )->c > 0;

        if ($hasPrimary || DB::table('wishlists')->count() > 0) {
            return;
        }

        DB::statement('ALTER TABLE `wishlists` MODIFY `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY');
        DB::statement('ALTER TABLE `wishlists` ADD UNIQUE `wishlists_user_cand_unique` (`user_id`, `cand_id`)');
    }

    public function down()
    {
        // Intentionally left empty: removing the primary key would break the
        // table again.
    }
};
