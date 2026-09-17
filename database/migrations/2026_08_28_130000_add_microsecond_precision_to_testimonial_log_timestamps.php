<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The Activity tab merges rows from two separate, independently
     * auto-incrementing tables and sorts purely by created_at. Second-level
     * precision (MySQL's default) is too coarse — several actions fired in
     * quick succession can land in the same second with no way to break the
     * tie correctly across tables. Microsecond precision makes that
     * effectively impossible for real, human-paced admin actions.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE testimonial_activity_logs MODIFY created_at TIMESTAMP(6) NULL, MODIFY updated_at TIMESTAMP(6) NULL');
        DB::statement('ALTER TABLE testimonial_approval_logs MODIFY created_at TIMESTAMP(6) NULL, MODIFY updated_at TIMESTAMP(6) NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE testimonial_activity_logs MODIFY created_at TIMESTAMP NULL, MODIFY updated_at TIMESTAMP NULL');
        DB::statement('ALTER TABLE testimonial_approval_logs MODIFY created_at TIMESTAMP NULL, MODIFY updated_at TIMESTAMP NULL');
    }
};
