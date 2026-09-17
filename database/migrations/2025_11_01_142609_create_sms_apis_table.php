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
        Schema::create('sms_apis', function (Blueprint $table) {
            $table->id();
            $table->string('api_name');         // Name of the API
            $table->text('api_base_url')->nullable(); 
            $table->unsignedBigInteger('staff_id');   // Staff ID (required, unsigned)
            $table->string('mobile_no');        // Mobile number or sender number
            $table->text('notes')->nullable();  // Optional notes or description
            $table->tinyInteger('status')->default(1); // 1 = active, 0 = inactive
            $table->text('api_assign_to')->nullable(); // Assigned user or service ID
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
        Schema::dropIfExists('sms_apis');
    }
};
