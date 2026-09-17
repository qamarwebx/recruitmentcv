<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('partners', function (Blueprint $table) {
            $table->string('partner_calling_number')->nullable()->after('portal_status');
            $table->string('partner_whatsapp_number')->nullable()->after('partner_calling_number');
            $table->unsignedBigInteger('partner_careoff_id')->nullable()->after('partner_whatsapp_number');
        });
    }

    public function down(): void
    {
        Schema::table('partners', function (Blueprint $table) {
            $table->dropColumn(['partner_calling_number', 'partner_whatsapp_number', 'partner_careoff_id']);
        });
    }
};