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
        Schema::create('todos', function (Blueprint $table) {
            $table->id();
            $table->string('task_title')->nullable();
            $table->text('task_description')->nullable();
            $table->integer('assignto_id')->nullable();
            $table->integer('todolabel_id')->nullable();
            $table->integer('todostatus_id')->nullabel();
            $table->datetime('start_on')->nullable();
            $table->date('finish_on')->nullable();
            $table->string('reminder_cycle')->nullable();
            $table->text('file_attachment')->nullable();
            $table->boolean('status')->default(true)->comment('0 => inactive, 1 => active');            
            $table->boolean('view_type')->default(true)->comment('0 => List, 1 => Kanban');
            $table->integer('admin_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('todos');
    }
};
