<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('all_contact_export_history_admin_filters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id')->unique();
            $table->string('status')->nullable();
            $table->unsignedBigInteger('filter_admin_id')->nullable();
            $table->string('list_name')->nullable();
            $table->string('business_type')->nullable();
            $table->date('created_from')->nullable();
            $table->date('created_to')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('all_contact_export_history_admin_filters');
    }
};
