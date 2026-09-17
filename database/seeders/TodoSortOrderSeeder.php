<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Todo;

class TodoSortOrderSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            'New Task',
            'In Process',
            'Complete',
            'Always',
            'Achieved',
            'Not Required'
        ];

        foreach ($statuses as $status) {

            $tasks = Todo::where('task_status', $status)
                ->orderBy('updated_at', 'asc')
                ->get();

            $order = 1;

            foreach ($tasks as $task) {
                $task->update([
                    'sort_order' => $order
                ]);

                $order++;
            }
        }
    }
}