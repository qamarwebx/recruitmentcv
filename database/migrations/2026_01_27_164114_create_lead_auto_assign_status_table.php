<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_auto_assign_status', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('status')
                  ->default(0)
                  ->comment('0 = deactive, 1 = active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_auto_assign_status');
    }
};
