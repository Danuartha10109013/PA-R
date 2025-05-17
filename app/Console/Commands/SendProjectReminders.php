<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Project;
use App\Models\ReminderProject;
use Illuminate\Console\Command;
use App\Mail\ProjectReminderMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendProjectReminders extends Command
{
    protected $signature = 'project:send-reminder';
    protected $description = 'Send reminder emails 1 day before project deadline';

    public function handle()
    {
        $tomorrow = Carbon::tomorrow()->toDateString();

        // Cari proyek yang deadline-nya besok
        $projects = Project::with(['reminders.user', 'user'])
            ->whereDate('end_date', $tomorrow)
            ->where('status', '!=', 'finished')
            ->get();

        if ($projects->isEmpty()) {
            $this->info("Tidak ada proyek yang deadline-nya besok ({$tomorrow})");
            return;
        }

        foreach ($projects as $project) {
            $this->sendReminders($project);
        }

        $this->info("Berhasil mengirim reminder untuk {$projects->count()} proyek!");
    }

    protected function sendReminders(Project $project)
    {
        // Kirim ke CEO
        $ceoUsers = User::where('role', 'ceo')->get();
        foreach ($ceoUsers as $ceo) {
            $this->sendEmail($ceo, $project, 'CEO');
        }

        // Kirim ke member yang terdaftar di reminder
        foreach ($project->reminders as $reminder) {
            if ($reminder->user->role === 'member') {
                $this->sendEmail($reminder->user, $project, 'Member');
            }
        }
    }

    protected function sendEmail($user, $project, $roleType)
    {
        try {
            Mail::to($user->email)
                ->send(new ProjectReminderMail($project, $user, $roleType));

            Log::info("Reminder terkirim ke {$roleType} {$user->email} untuk proyek {$project->name}");
            $this->info("Reminder terkirim ke {$roleType} {$user->email}");
        } catch (\Exception $e) {
            Log::error("Gagal mengirim reminder ke {$roleType} {$user->email}: " . $e->getMessage());
            $this->error("Error: " . $e->getMessage());
        }
    }
}
