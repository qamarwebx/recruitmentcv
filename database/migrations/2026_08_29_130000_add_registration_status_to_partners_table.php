<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * registration_status: 0 = Pending, 1 = Approved, 2 = Rejected.
     * Distinct from `status` (active/inactive, tied to service charges) and
     * `portal_status` (published on the public booking portal) - neither of
     * those represents partner self-registration approval today.
     */
    public function up()
    {
        Schema::table('partners', function (Blueprint $table) {
            $table->tinyInteger('registration_status')->default(0)->after('portal_status');
            $table->timestamp('mobile_verified_at')->nullable()->after('registration_status');
            $table->unsignedBigInteger('admin_id')->nullable()->change();
            $table->unique('owner_mobile_no');
        });
    }

    public function down()
    {
        Schema::table('partners', function (Blueprint $table) {
            $table->dropUnique(['owner_mobile_no']);
            $table->unsignedBigInteger('admin_id')->nullable(false)->change();
            $table->dropColumn(['registration_status', 'mobile_verified_at']);
        });
    }
};
