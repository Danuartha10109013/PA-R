<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Project;
use App\Models\ReminderProject;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProjectReminderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $project = Project::first(); // Ambil proyek pertama untuk uji coba
        $reminderDate = Carbon::parse($project->start_date)->subDay(); // H-1

        ReminderProject::create([
            'project_id' => $project->id,
            'user_id' => $project->user_id,
            'reminder_date' => $reminderDate->toDateString(),
        ]);
    }
}
