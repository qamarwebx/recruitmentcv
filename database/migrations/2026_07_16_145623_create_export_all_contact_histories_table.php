<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('export_all_contact_histories', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('admin_id')->nullable();

            $table->string('file_name')->nullable();
            $table->string('file_path')->nullable();

            $table->enum('status', [
                'pending',
                'processing',
                'completed',
                'failed'
            ])->default('pending');

            $table->json('columns')->nullable();
            $table->longText('contact_ids')->nullable();

            $table->boolean('is_all_export')->default(false);

            $table->unsignedBigInteger('total_records')->default(0);
            $table->unsignedBigInteger('exported_records')->default(0);

            $table->text('error_message')->nullable();

            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            $table->index('admin_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('export_all_contact_histories');
    }
};