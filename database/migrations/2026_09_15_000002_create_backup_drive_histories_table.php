<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Google Drive upload history for the "Backup Management - Google
     * Drive" table (Settings > Backup > DB Backup). One row per local
     * backup_histories row that Drive upload was attempted for - the
     * unique index on backup_history_id is what makes an upload retry
     * (scheduler tick re-processing the same backup, or a retried queue
     * job) update the existing row instead of creating a duplicate.
     */
    public function up()
    {
        Schema::create('backup_drive_histories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('backup_history_id')
                ->nullable()
                ->unique()
                ->constrained('backup_histories')
                ->nullOnDelete();

            // Copied from the parent backup_histories row at upload time so
            // this table can be listed/filtered/retained on its own.
            $table->string('backup_reference');
            $table->string('type', 20);
            $table->unsignedBigInteger('file_size')->nullable();

            $table->string('google_account_email')->nullable();
            $table->string('drive_file_id')->nullable();
            $table->string('drive_folder_id')->nullable();
            $table->string('drive_view_link')->nullable();

            // pending | uploaded | failed
            $table->string('status', 20)->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamp('uploaded_at')->nullable();

            $table->timestamps();

            // Drive retention and history listing both filter by
            // type (+status) and sort by created_at, same shape as
            // backup_histories' composite index.
            $table->index(['type', 'status', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('backup_drive_histories');
    }
};
