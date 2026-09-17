<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('deal_pipeline', function (Blueprint $table) {
            $table->id();
        
            $table->unsignedBigInteger('business_id')->nullable();           // Business
            $table->unsignedBigInteger('deal_stage_id')->nullable();  // Deal Stage
            $table->unsignedBigInteger('recruite_status_id')->nullable(); // Recruit Status
            $table->unsignedBigInteger('associate_id')->nullable(); // Recruit Status
            $table->string('job_title')->nullable();         // job_title
            $table->string('deal_name')->nullable();         // Deal Name
            $table->string('name')->nullable();              // Name
            $table->string('company')->nullable();           // Company
            $table->string('candidate')->nullable();         // Candidate
            $table->string('passport_no')->nullable();       // Passport No.
            $table->string('amount')->nullable();       // Amount.
            $table->text('notes')->nullable();              // Notes
            $table->string('contact')->nullable();           // Contact
            $table->string('contact_whatsapp')->nullable();           // contact_whatsapp
            $table->string('email')->nullable();             // Email
            $table->string('country')->nullable();           // Country
            $table->string('city')->nullable();              // City
            $table->string('care_of')->nullable();           // Care Of
            $table->string('close_date')->nullable();          // Close Date
            $table->unsignedBigInteger('created_by')->nullable(); // Created By
            $table->unsignedBigInteger('modified_by')->nullable(); // Modified By
            $table->string('source')->nullable();            // Source
        
            $table->timestamps();
        
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deal_pipeline');
    }
};

	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
 