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
        Schema::create('adminpermissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staff_id');
            $table->boolean('bookings')->default(false);
            $table->boolean('employer')->default(false);
            $table->boolean('candidate')->default(false);
            $table->boolean('client')->default(false);
            $table->boolean('partner')->default(false);
            $table->boolean('settings')->default(false);
            $table->boolean('testing')->default(false);
            $table->boolean('staff')->default(false);
            $table->boolean('profession')->default(false);
            $table->boolean('placeofissue')->default(false);
            $table->boolean('country')->default(false);
            $table->boolean('region')->default(false);
            $table->boolean('city')->default(false);
            $table->boolean('websiteconfig')->default(false);
            $table->boolean('mailsetup')->default(false);
            $table->boolean('carknown')->default(false);
            $table->boolean('booking_confirm')->default(false);
            $table->boolean('add_visa_details')->default(false);
            $table->boolean('view_booking')->default(false);
            $table->boolean('add_payment')->default(false);
            $table->boolean('cancel_booking')->default(false);
            $table->boolean('replace_candidate')->default(false);
            $table->boolean('view_employer')->default(false);
            $table->boolean('delete_employer')->default(false);
            $table->boolean('add_candidate')->default(false);
            $table->boolean('edit_candidate')->default(false);
            $table->boolean('view_candidate')->default(false);
            $table->boolean('delete_candidate')->default(false);
            $table->boolean('publish_candidate')->default(false);
            $table->boolean('view_client')->default(false);
            $table->boolean('delete_client')->default(false);
            $table->boolean('add_partner')->default(false);
            $table->boolean('edit_partner')->default(false);
            $table->boolean('delete_partner')->default(false);
            $table->boolean('view_partner')->default(false);
            $table->boolean('add_staff')->default(false);
            $table->boolean('edit_staff')->default(false);
            $table->boolean('delete_staff')->default(false);
            $table->boolean('view_staff')->default(false);
            $table->boolean('add_profession')->default(false);
            $table->boolean('edit_profession')->default(false);
            $table->boolean('view_profession')->default(false);
            $table->boolean('delete_profession')->default(false);
            $table->boolean('add_placeofissue')->default(false);
            $table->boolean('edit_placeofissue')->default(false);
            $table->boolean('view_placeofissue')->default(false);
            $table->boolean('delete_placeofissue')->default(false);
            $table->boolean('add_country')->default(false);
            $table->boolean('edit_country')->default(false);
            $table->boolean('view_country')->default(false);
            $table->boolean('delete_country')->default(false);
            $table->boolean('add_region')->default(false);
            $table->boolean('edit_region')->default(false);
            $table->boolean('delete_region')->default(false);
            $table->boolean('view_region')->default(false);
            $table->boolean('add_city')->default(false);
            $table->boolean('edit_city')->default(false);
            $table->boolean('view_city')->default(false);
            $table->boolean('delete_city')->default(false);
            $table->boolean('add_mailsetuo')->default(false);
            $table->boolean('edit_mailsetup')->default(false);
            $table->boolean('delete_mailsetup')->default(false);
            $table->boolean('view_mailsetup')->default(false);
            $table->boolean('add_car_known')->default(false);
            $table->boolean('edit_car_known')->default(false);
            $table->boolean('delete_car_known')->default(false);
            $table->boolean('view_car_known')->default(false);
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
        Schema::dropIfExists('adminpermissions');
    }
};
