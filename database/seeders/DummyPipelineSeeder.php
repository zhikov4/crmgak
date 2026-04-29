<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pipeline;
use App\Models\Lead;

class DummyPipelineSeeder extends Seeder
{
    public function run(): void
    {
        $leads = Lead::take(6)->get();
        $stages = ['new','contacted','proposal','negotiation','won','won'];
        $values = [15000000, 25000000, 50000000, 35000000, 75000000, 60000000];

        foreach ($leads as $i => $lead) {
            Pipeline::create([
                'lead_id'              => $lead->id,
                'stage'                => $stages[$i],
                'value'                => $values[$i],
                'expected_close_date'  => now()->addDays(rand(7, 60)),
                'assigned_to'          => 1,
                'order'                => $i,
            ]);
        }
    }
}
