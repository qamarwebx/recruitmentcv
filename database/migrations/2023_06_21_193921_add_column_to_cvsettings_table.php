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
        Schema::table('cvsettings', function (Blueprint $table) {
            $table->string('image1')->nullable()->after('font_family');
            $table->string('image2')->nullable()->after('font_family');
            $table->string('image3')->nullable()->after('font_family');
            $table->string('image4')->nullable()->after('font_family');
            $table->string('image5')->nullable()->after('font_family');
            $table->string('width')->nullable()->after('font_family');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cvsettings', function (Blueprint $table) {
            $table->dropColumn('image1');
            $table->dropColumn('image2');
            $table->dropColumn('image3');
            $table->dropColumn('image4');
            $table->dropColumn('image5');
            $table->dropColumn('width');
        });
    }
};
