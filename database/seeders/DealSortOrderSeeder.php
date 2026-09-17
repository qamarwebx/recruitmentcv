<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DealPipeline;

class DealSortOrderSeeder extends Seeder
{
    public function run(): void
    {
        /* group by stage (IMPORTANT for Kanban) */
        $stages = DealPipeline::select('deal_stage_id')->distinct()->pluck('deal_stage_id');

        foreach ($stages as $stageId) {

            $deals = DealPipeline::where('deal_stage_id', $stageId)
                ->orderBy('updated_at', 'desc')
                ->get();

            foreach ($deals as $index => $deal) {

                $deal->update([
                    'sort_order' => $index + 1
                ]);
            }
        }

        $this->command->info('Deal sort_order updated successfully (updated_at DESC)');
    }
}