<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Project;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Project::create([
            'user_id' => 2, // ID user yang membuat proyek
            'name' => 'Project Test Reminder H-1',
            'description' => 'Test project for H-1 reminder functionality',
            'start_date' => Carbon::now()->addDay(), // Tanggal mulai besok
            'end_date' => Carbon::now()->addDays(5), // Tanggal selesai beberapa hari setelahnya
            'status' => 'not_started',
            'budget' => 10000.00,
        ]);
    }
}
