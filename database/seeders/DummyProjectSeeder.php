<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\Lead;

class DummyProjectSeeder extends Seeder
{
    public function run(): void
    {
        $leads = Lead::take(4)->get();
        $projects = [
            ['name' => 'Renovasi Kantor PT Maju Bersama', 'status' => 'in_progress', 'priority' => 'high', 'value' => 75000000, 'progress' => 45],
            ['name' => 'Website Company Profile CV Sukses', 'status' => 'in_progress', 'priority' => 'medium', 'value' => 25000000, 'progress' => 70],
            ['name' => 'Sistem CRM PT Karya Abadi', 'status' => 'planning', 'priority' => 'high', 'value' => 120000000, 'progress' => 10],
            ['name' => 'Digital Marketing UD Rejeki', 'status' => 'completed', 'priority' => 'low', 'value' => 15000000, 'progress' => 100],
        ];

        foreach ($projects as $i => $project) {
            Project::create(array_merge($project, [
                'lead_id'        => $leads[$i]->id ?? null,
                'start_date'     => now()->subDays(rand(10, 60)),
                'due_date'       => now()->addDays(rand(14, 90)),
                'created_by'     => 1,
                'assigned_to'    => 1,
            ]));
        }
    }
}
