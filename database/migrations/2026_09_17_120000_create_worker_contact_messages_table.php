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
        // Public Worker Portal (worker.qamarhire.com) "Contact Us" form
        // submissions. Deliberately its own small table rather than the
        // CRM's Contactp/Allcontact (bulk marketing contact-list) or Lead
        // (sales pipeline) models - both are heavyweight, campaign/
        // assignment-oriented systems built for a different workflow, and
        // reusing either here would risk side effects on those unrelated,
        // actively-used features for what is just an inbound enquiry log.
        Schema::create('worker_contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->text('message');
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
        Schema::dropIfExists('worker_contact_messages');
    }
};
