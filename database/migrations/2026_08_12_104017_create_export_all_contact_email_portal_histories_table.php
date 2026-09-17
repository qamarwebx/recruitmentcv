<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('export_all_contact_email_portal_histories', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('admin_id')->nullable();

            $table->text('api_token')->nullable();

            $table->string('list_uid')->nullable();
            $table->string('list_name')->nullable();

            $table->string('business_type')->nullable();

            $table->json('industry_ids')->nullable();
            $table->string('industry_names')->nullable();

            $table->json('filters')->nullable();

            $table->enum('status', [
                'pending',
                'processing',
                'completed',
                'completed_with_errors',
                'failed'
            ])->default('pending');

            $table->unsignedBigInteger('total_records')->default(0);
            $table->unsignedBigInteger('pending_count')->default(0);
            $table->unsignedBigInteger('success_count')->default(0);
            $table->unsignedBigInteger('failed_count')->default(0);

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
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('export_all_contact_email_portal_histories');
    }
};
