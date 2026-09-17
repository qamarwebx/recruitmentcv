<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deal_admin_save_filters', function (Blueprint $table) {
            $table->id();
            $table->string('priority')->nullable(); // priority
            $table->unsignedBigInteger('admin_id')->unique(); // Each admin has only one saved filter
            $table->string('business_id')->nullable();  // store selected business IDs as comma-separated
            $table->string('associate_id')->nullable(); // selected associates
            $table->string('care_of')->nullable();   // selected care_of
            $table->string('source')->nullable();       // selected source(s)
            $table->string('created_date')->nullable();
            $table->string('updated_date')->nullable();
            $table->string('created_by')->nullable();   // selected creator IDs
            $table->tinyInteger('switch_to')->default(0)->comment('0 = list, 1 = kanban');
            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deal_admin_save_filters');
    }
};
