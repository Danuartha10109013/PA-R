<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectReminderTestSeeder extends Seeder
{
    public function run()
    {
        // Ambil beberapa user
        $owner = User::find(1);
        $member1 = User::find(2);
        $member2 = User::find(3);

        // Buat project yang akan dimulai besok
        $project = Project::create([
            'user_id' => 2,
            'name' => 'Project Test Reminder',
            'description' => 'Ini adalah project untuk testing reminder',
            'start_date' => now()->addDay()->format('Y-m-d'), // Otomatis besok
            'end_date' => now()->addDays(7)->format('Y-m-d'),
            'status' => 'not_started',
            'budget' => 10000000
        ]);

        // Tambahkan anggota tim
        $project->users()->attach([$member1->id, $member2->id]);

        $this->command->info('Project test reminder created:');
        $this->command->info("- Name: {$project->name}");
        $this->command->info("- Start Date: {$project->start_date}");
        $this->command->info("- Team: {$owner->name} (owner), {$member1->name}, {$member2->name}");
    }
}
