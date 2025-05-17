<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Models\ReminderProject;
use Illuminate\Console\Command;
use App\Mail\ProjectReminderMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendProjectReminders extends Command
{
    protected $signature = 'project:send-reminder';
    protected $description = 'Send reminder emails 1 day before project deadline';

    public function handle()
    {
        $tomorrow = Carbon::tomorrow()->toDateString();

        // Cari reminder yang terkait dengan proyek yang deadline-nya besok
        $reminders = ReminderProject::with(['project', 'user'])
            ->whereHas('project', function ($query) use ($tomorrow) {
                $query->whereDate('end_date', $tomorrow) // Menggunakan end_date sebagai deadline
                    ->where('status', '!=', 'finished');
            })
            ->get();

        if ($reminders->isEmpty()) {
            $this->info("Tidak ada reminder untuk proyek yang deadline-nya besok ({$tomorrow})");
            return;
        }

        foreach ($reminders as $reminder) {
            $this->sendReminder($reminder);
        }

        $this->info("Berhasil mengirim {$reminders->count()} reminder!");
    }

    protected function sendReminder(ReminderProject $reminder)
    {
        try {
            Mail::to($reminder->user->email)
                ->send(new ProjectReminderMail($reminder));

            Log::info("Reminder terkirim ke {$reminder->user->email} untuk proyek {$reminder->project->name}");
            $this->info("Reminder terkirim ke {$reminder->user->email}");
        } catch (\Exception $e) {
            Log::error("Gagal mengirim reminder ke {$reminder->user->email}: " . $e->getMessage());
            $this->error("Error: " . $e->getMessage());
        }
    }
}
