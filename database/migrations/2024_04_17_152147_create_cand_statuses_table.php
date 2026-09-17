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
        Schema::create('cand_statuses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cand_id');
            $table->boolean('new_candidate')->default(false);
            $table->boolean('candidate_are_ready')->default(false);
            $table->boolean('published_for_selection')->default(false);
            $table->boolean('selected')->default(false);
            $table->boolean('visa_received')->default(false);
            $table->boolean('passport_in_embassy')->default(false);
            $table->boolean('visa_stamped')->default(false);
            $table->boolean('applied_for_emigration')->default(false);
            $table->boolean('emigration_approved')->default(false);
            $table->boolean('waiting_for_flight_ticket')->default(false);
            $table->boolean('ticket_confirmed')->default(false);
            $table->boolean('deployed')->default(false);
            $table->boolean('cancelled')->default(false);
            $table->boolean('hold')->default(false);
            $table->boolean('visa_cencelled')->default(false);
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
        Schema::dropIfExists('cand_statuses');
    }
};
