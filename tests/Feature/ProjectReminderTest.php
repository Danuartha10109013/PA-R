<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Project;
use App\Mail\ProjectReminderMail;
use Illuminate\Support\Facades\Mail;
use App\Console\Commands\SendProjectReminders;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProjectReminderTest extends TestCase
{
    use RefreshDatabase;

    public function test_reminder_email_sent()
    {
        Mail::fake();

        $user = User::factory()->create();
        $project = Project::factory()->create([
            'user_id' => $user->id,
            'start_date' => now()->addDay(),
            'status' => 'in_progress'
        ]);

        $this->artisan('reminders:send')
            ->assertExitCode(0);

        Mail::assertSent(ProjectReminderMail::class, function ($mail) use ($project) {
            return $mail->project->id === $project->id;
        });
    }

    public function test_no_email_sent_for_completed_projects()
    {
        Mail::fake();

        $user = User::factory()->create();
        $project = Project::factory()->create([
            'user_id' => $user->id,
            'start_date' => now()->addDay(),
            'status' => 'completed'
        ]);

        $this->artisan('reminders:send')
            ->assertExitCode(0);

        Mail::assertNotSent(ProjectReminderMail::class);
    }
}
