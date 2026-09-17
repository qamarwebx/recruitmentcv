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
        Schema::create('contactpluses', function (Blueprint $table) {
            $table->id();
            $table->string('office_eng_name',150)->nullable();
            $table->string('office_ar_name',150)->nullable();
            $table->string('office_no',25)->nullable();
            $table->string('office_email',65)->nullable();
            $table->string('owner_name',75)->nullable();
            $table->string('owner_contact',25)->nullable();
            $table->string('owenr_email',75)->nullable();
            $table->integer('country_id');
            $table->integer('city_id')->nullable();
            $table->string('prim_concern_name',75)->nullable();
            $table->string('prim_contact',25)->nullable();
            $table->string('prim_email',75)->nullable();
            $table->string('sec_concern_name',75)->nullable();
            $table->string('sec_contact',25)->nullable();
            $table->string('sec_email',75)->nullable();
            $table->string('concern_name3',75)->nullable();
            $table->string('contact3',25)->nullable();
            $table->string('concern_name4',75)->nullable();
            $table->string('contact4',25)->nullable();
            $table->string('concern_name5',75)->nullable();
            $table->string('contact5',25)->nullable();
            $table->string('concern_name6',75)->nullable();
            $table->string('contact6',25)->nullable();
            $table->boolean('status')->default(true);
            $table->integer('cstatus_id')->nullable();
            $table->integer('staff_id');
            $table->string('office_logo',200)->nullable();
            $table->integer('lcs_id')->nullable();
            $table->integer('ls_id')->nullable();
            $table->integer('businesstype_id')->nullable();
            $table->integer('careoff_id')->nullable();
            $table->integer('leadowner_id')->nullable();
            $table->integer('group_id')->nullable();
            $table->boolean('subscribe')->default(true);
            $table->string('contact7',20)->nullable();
            $table->string('contact8',20)->nullable();
            $table->string('contact9',20)->nullable();
            $table->string('contact10',20)->nullable();
            $table->string('contact11',20)->nullable();
            $table->string('contact12',20)->nullable();
            $table->text('send_tag')->nullable();
            $table->text('send_date')->nullable();
            $table->datetime('unsubscribe_date')->nullable();
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
        Schema::dropIfExists('contactpluses');
    }
};
