<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('metawhatsappcampaigns', function (Blueprint $table) {
            $table->text('leads_job_title')->nullable()->after('assign_var'); 
            $table->text('leads_country')->nullable()->after('leads_job_title');
            $table->text('leads_date')->nullable()->after('leads_country');
        });
    }

    public function down()
    {
        Schema::table('metawhatsappcampaigns', function (Blueprint $table) {
            $table->dropColumn(['leads_job_title', 'leads_country', 'leads_date']);
        });
    }
};
