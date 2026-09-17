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
        Schema::create('todoadminsavefilters', function (Blueprint $table) {
            $table->id();
            $table->integer('admin_id');
            $table->text('department_id')->nullable();
            $table->text('todolabel_id')->nullable();
            $table->text('priority')->nullable();
            $table->text('task_status')->nullable();
            $table->text('assignto_id')->nullable();
            $table->text('start_date')->nullable();
            $table->text('finish_date')->nullable();
            $table->text('complete_date_range')->nullable();
            $table->text('achieved_date_range')->nullable();
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
        Schema::dropIfExists('todoadminsavefilters');
    }
};
