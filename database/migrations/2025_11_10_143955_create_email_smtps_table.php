<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_smtps', function (Blueprint $table) {
            $table->id();

            // General info
            $table->string('smtp_name')->nullable(); // Custom name for SMTP record
            $table->string('mail_mailer')->default('smtp');
            $table->string('mail_host');
            $table->integer('mail_port');
            $table->string('mail_username');
            $table->string('mail_password');
            $table->string('mail_encryption')->nullable();

            // From details
            $table->string('from_address');
            $table->string('from_name');

            // Optional info
            $table->text('notes')->nullable();
            $table->text('smtp_assign_to')->nullable(); // Comma-separated staff/user IDs
            $table->unsignedBigInteger('staff_id')->nullable(); // created by / owned by

            // Status & timestamps
            $table->boolean('status')->default(1); // active/inactive
            $table->timestamps();

            // Index for faster filtering
            $table->index(['staff_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_smtps');
    }
};
