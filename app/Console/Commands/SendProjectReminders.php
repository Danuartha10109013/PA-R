<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Project;
use App\Models\Notification;
use Illuminate\Console\Command;
use App\Mail\ProjectReminderMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendProjectReminders extends Command
{
    protected $signature = 'reminders:send';
    protected $description = 'Send reminder emails for upcoming projects';

    public function handle()
    {
        $tomorrow = now()->addDay()->format('Y-m-d');
        $this->info("Checking projects for date: {$tomorrow}");

        $projects = Project::whereDate('start_date', $tomorrow)
            ->where('status', '!=', 'completed')
            ->with(['users', 'notifications' => function ($query) {
                $query->where('type', 'reminder')
                    ->whereDate('created_at', today());
            }])
            ->get();

        foreach ($projects as $project) {
            $this->notifyProjectOwner($project);
            $this->notifyTeamMembers($project);
        }

        $this->info('Reminder emails sent successfully.');
    }

    protected function notifyProjectOwner(Project $project)
    {
        if (!$this->alreadyNotified($project, $project->user)) {
            $this->sendEmailAndCreateNotification($project, $project->user);
        }
    }

    protected function notifyTeamMembers(Project $project)
    {
        foreach ($project->users as $user) {
            if (!$this->alreadyNotified($project, $user)) {
                $this->sendEmailAndCreateNotification($project, $user);
            }
        }
    }

    protected function alreadyNotified(Project $project, $user)
    {
        return $project->notifications
            ->where('user_id', $user->id)
            ->isNotEmpty();
    }

    protected function sendEmailAndCreateNotification(Project $project, $user)
    {
        try {
            Mail::to($user->email)->send(new ProjectReminderMail($project));

            Notification::create([
                'project_id' => $project->id,
                'user_id' => $user->id,
                'type' => 'reminder',
                'message' => 'Reminder untuk project ' . $project->name . ' yang dimulai besok',
                'sent_at' => now(),
                'is_sent' => true
            ]);
        } catch (\Exception $e) {
            Log::error("Gagal mengirim reminder untuk project {$project->id} ke user {$user->id}: " . $e->getMessage());

            Notification::create([
                'project_id' => $project->id,
                'user_id' => $user->id,
                'type' => 'reminder',
                'message' => 'Reminder untuk project ' . $project->name . ' (GAGAL TERKIRIM)',
                'is_sent' => false
            ]);
        }
    }
}
