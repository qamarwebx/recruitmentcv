<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    public function run()
    {
        $adminId = Admin::where('user_type', 1)->value('id') ?? Admin::value('id') ?? 1;

        foreach (['Lucknow', 'New Delhi', 'Mumbai', 'Hyderabad'] as $name) {
            Branch::firstOrCreate(
                ['name' => $name],
                ['admin_id' => $adminId, 'status' => true]
            );
        }
    }
}
