<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Single-row settings + connection table for the Google Drive backup
     * feature (Settings > Backup > DB Backup > Google Drive), mirroring
     * how backup_settings holds the Daily/Interval settings singleton -
     * see App\Models\BackupGoogleAccount::current().
     *
     * access_token/refresh_token are stored via Laravel's 'encrypted' cast
     * (same convention as Admin::email_qamr_api_token), so the raw values
     * never sit in the database in plaintext.
     */
    public function up()
    {
        Schema::create('backup_google_accounts', function (Blueprint $table) {
            $table->id();

            // Feature toggle, independent from connection state - an admin
            // can connect an account and still pause uploads without
            // disconnecting.
            $table->boolean('enabled')->default(false);

            $table->string('google_id')->nullable();
            $table->string('google_email')->nullable();
            $table->string('google_name')->nullable();

            $table->text('access_token')->nullable();
            $table->text('refresh_token')->nullable();
            $table->timestamp('token_expires_at')->nullable();
            $table->string('scope')->nullable();

            // Dedicated backup folder created in the connected account's
            // Drive on first use.
            $table->string('drive_folder_id')->nullable();
            $table->string('drive_folder_name')->nullable();

            // disconnected | connected | error
            $table->string('status', 20)->default('disconnected');
            $table->text('last_error')->nullable();
            $table->timestamp('last_tested_at')->nullable();
            $table->timestamp('connected_at')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('backup_google_accounts');
    }
};
