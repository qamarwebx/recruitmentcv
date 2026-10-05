<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Additive only: Partner Portal team members. Each row belongs to exactly
 * one partner (partner_id) and signs in on the existing Partner Login with
 * its own username / email / mobile / password / Google identity;
 * `permissions` = {module: [view, create, update, delete]} granted by that
 * partner. No existing table or row is changed.
 */
return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('partner_team_members')) {
            return;
        }

        Schema::create('partner_team_members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('partner_id')->index();
            $table->string('full_name');
            $table->string('username', 120)->nullable()->unique();
            $table->string('email')->nullable()->unique();
            $table->string('country_code', 5)->nullable();
            $table->string('mobile', 20)->nullable()->index();
            $table->string('password')->nullable();
            $table->string('google_id')->nullable()->unique();
            $table->json('permissions')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('partner_team_members');
    }
};
