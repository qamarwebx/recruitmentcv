<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('file_manager_items', function (Blueprint $table) {
            $table->boolean('favorite')->default(false)->after('metadata');
            $table->timestamp('last_opened_at')->nullable()->after('favorite');
            $table->string('color', 20)->nullable()->after('last_opened_at');

            $table->index('favorite');
            $table->index('last_opened_at');
        });
    }

    public function down()
    {
        Schema::table('file_manager_items', function (Blueprint $table) {
            $table->dropIndex(['favorite']);
            $table->dropIndex(['last_opened_at']);
            $table->dropColumn(['favorite', 'last_opened_at', 'color']);
        });
    }
};
