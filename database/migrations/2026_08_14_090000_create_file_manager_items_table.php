<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('file_manager_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('owner_id');
            $table->string('owner_user_type')->default('admin');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('type', 10); // folder | file
            $table->string('name');
            $table->string('storage_path')->nullable();
            $table->string('original_name')->nullable();
            $table->string('extension', 20)->nullable();
            $table->string('mime_type', 150)->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->string('disk')->default('file_manager');
            $table->json('metadata')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('owner_id')->references('id')->on('admins')->cascadeOnDelete();
            $table->foreign('parent_id')->references('id')->on('file_manager_items')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('admins')->nullOnDelete();
            $table->foreign('updated_by')->references('id')->on('admins')->nullOnDelete();

            $table->index('owner_id');
            $table->index('parent_id');
            $table->index('type');
            $table->index('deleted_at');
            $table->index('created_by');
            $table->index('updated_at');
            $table->index('name');
            $table->index('size');
            $table->index('mime_type');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('file_manager_items');
    }
};
