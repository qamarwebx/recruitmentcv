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
        Schema::create('google_accounts', function (Blueprint $table) {
            $table->id();
        
            $table->unsignedBigInteger('admin_id')->nullable();
        
            $table->string('google_id')->unique();
            $table->string('name')->nullable();
            $table->string('email')->unique();
        
            $table->longText('access_token')->nullable();
            $table->longText('refresh_token')->nullable();
        
            $table->timestamp('token_expires_at')->nullable();
        
            $table->timestamp('last_synced_at')->nullable();
        
            $table->unsignedInteger('total_contacts')->default(0);
        
            $table->boolean('is_active')->default(true);
        
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
        Schema::dropIfExists('google_accounts');
    }
};
