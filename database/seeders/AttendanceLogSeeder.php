<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\AttendanceLog;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AttendanceLogSeeder extends Seeder
{
    private const DAYS = 60;

    private const TARGET_ADMIN_ID = 31;

    private array $lateReasons = [
        'Traffic',
        'Medical Appointment',
        'Vehicle Issue',
        'Family Emergency',
        'Public Transport Delay',
    ];

    private array $rejectionReasons = [
        'Attendance time does not match office records.',
        'Late arrival without a valid reason.',
        'Discrepancy in reported time.',
        'Incorrect attendance entry, please resubmit.',
    ];

    public function run(): void
    {
        // Only reseed the target admin's own records - other staff members'
        // attendance data is left untouched.
        AttendanceLog::where('admin_id', self::TARGET_ADMIN_ID)->delete();

        $superAdmin = Admin::where('user_type', 1)->first();

        if (!$superAdmin) {
            $this->command?->warn('No Super Admin found - skipping attendance seeding.');
            return;
        }

        // Attendance subject: TARGET_ADMIN_ID only.
        $staff = Admin::where('id', self::TARGET_ADMIN_ID)->get();

        if ($staff->isEmpty()) {
            $this->command?->warn('Admin id ' . self::TARGET_ADMIN_ID . ' not found - skipping attendance seeding.');
            return;
        }

        // Who can approve/reject a Time In: any active Super Admin or Team Head.
        $approvers = Admin::where('status', 1)
            ->get()
            ->filter(fn ($admin) => $admin->user_type == 1 || $this->isManager($admin))
            ->values();

        $this->command?->info("Seeding attendance for {$staff->count()} staff member(s) across " . self::DAYS . ' days...');

        $rows = [];

        foreach ($staff as $admin) {
            for ($daysAgo = self::DAYS - 1; $daysAgo >= 0; $daysAgo--) {
                $date = Carbon::today()->subDays($daysAgo);

                // Time In: random minute between 08:30 and 11:30.
                $inTime = $date->copy()->setTime(8, 30)->addMinutes(random_int(0, 180));

                // Time Out: random minute between 17:00 and 20:30.
                // Ranges never overlap, so Time Out > Time In always holds.
                $outTime = $date->copy()->setTime(17, 0)->addMinutes(random_int(0, 210));

                $isLate = $inTime->format('H:i') > '10:00';
                $inStatus = $this->randomTimeInStatus($daysAgo);

                $approvedBy = null;
                $approvedAt = null;
                $rejectionReason = null;

                if ($inStatus !== 'waiting') {
                    $approver = $approvers->random();
                    $approvedBy = $approver->id;
                    $approvedAt = $inTime->copy()->addMinutes(random_int(10, 240));

                    if ($inStatus === 'rejected') {
                        $rejectionReason = fake()->randomElement($this->rejectionReasons);
                    }
                }

                // Time In
                $rows[] = [
                    'admin_id' => $admin->id,
                    'attendance_type' => 'in',
                    'attendance_time' => $inTime->format('Y-m-d H:i:s'),
                    'reason' => $isLate ? fake()->randomElement($this->lateReasons) : null,
                    'status' => $inStatus,
                    'approved_by' => $approvedBy,
                    'approved_at' => $approvedAt?->format('Y-m-d H:i:s'),
                    'rejection_reason' => $rejectionReason,
                    'created_at' => $inTime->format('Y-m-d H:i:s'),
                    'updated_at' => ($approvedAt ?? $inTime)->format('Y-m-d H:i:s'),
                ];

                // Time Out - always auto-approved, no reason, approved by Super Admin
                // at the moment of the punch itself.
                $rows[] = [
                    'admin_id' => $admin->id,
                    'attendance_type' => 'out',
                    'attendance_time' => $outTime->format('Y-m-d H:i:s'),
                    'reason' => null,
                    'status' => 'approved',
                    'approved_by' => $superAdmin->id,
                    'approved_at' => $outTime->format('Y-m-d H:i:s'),
                    'rejection_reason' => null,
                    'created_at' => $outTime->format('Y-m-d H:i:s'),
                    'updated_at' => $outTime->format('Y-m-d H:i:s'),
                ];
            }
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            AttendanceLog::insert($chunk);
        }

        $this->command?->info('Attendance seeding complete: ' . count($rows) . ' records created.');
    }

    /**
     * Recent days are biased toward "waiting" (approval hasn't caught up yet);
     * older days settle into a realistic 70% approved / 15% rejected / 15% waiting mix.
     */
    private function randomTimeInStatus(int $daysAgo): string
    {
        if ($daysAgo <= 2) {
            return fake()->randomElement(['waiting', 'waiting', 'approved', 'rejected']);
        }

        $roll = random_int(1, 100);

        if ($roll <= 70) {
            return 'approved';
        }

        if ($roll <= 85) {
            return 'rejected';
        }

        return 'waiting';
    }

    private function isManager($admin): bool
    {
        $roles = json_decode($admin->role, true) ?: [];
        return in_array('Team Head', $roles);
    }
}
