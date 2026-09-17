<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('adminpermissions', function (Blueprint $table) {
            $table->boolean('testimonial')->default(false);
            $table->boolean('add_testimonial')->default(false);
            $table->boolean('edit_testimonial')->default(false);
            $table->boolean('view_testimonial')->default(false);
            $table->boolean('delete_testimonial')->default(false);
        });
    }

    public function down()
    {
        Schema::table('adminpermissions', function (Blueprint $table) {
            $table->dropColumn([
                'testimonial',
                'add_testimonial',
                'edit_testimonial',
                'view_testimonial',
                'delete_testimonial',
            ]);
        });
    }
};
