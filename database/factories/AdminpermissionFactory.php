<?php

namespace Database\Factories;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Adminpermission>
 */
class AdminpermissionFactory extends Factory
{
    protected $model = \App\Models\Adminpermission::class;

    public function definition()
    {
        return [
            'staff_id' => Admin::factory(),
            'full_access' => false,
        ];
    }
}
