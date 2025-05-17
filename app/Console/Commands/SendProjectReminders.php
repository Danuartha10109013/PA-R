<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Models\ReminderProject;
use Illuminate\Console\Command;
use App\Mail\ProjectReminderMail;
use App\Policies\ReminderPolicy;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendProjectReminders extends Command
{
    protected $signature = 'project:send-reminder';
    protected $description = 'Send project reminder emails to users';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // $reminders = ReminderProject::where('reminder_date', now()->toDateString())->get();
        // Ambil pengingat dengan tanggal hari ini
        $reminders = ReminderProject::where('reminder_date', now()->toDateString())
            ->get()
            ->filter(function ($reminder) {
                // Log untuk memverifikasi status proyek
                Log::info('Project Status: ' . $reminder->project->status);

                // Hanya ambil proyek dengan status 'not_started'
                return $reminder->project->status == 'pending';
            });

        // Jika tidak ada pengingat yang cocok
        if ($reminders->isEmpty()) {
            Log::info('No reminders for projects with status "not_started"');
        }

        foreach ($reminders as $reminder) {
            try {
                // Kirim email
                Mail::to($reminder->user->email)->send(new ProjectReminderMail($reminder));

                // Log untuk memastikan email dikirim
                Log::info('Reminder email sent to: ' . $reminder->user->email);

                $this->info('Reminder email sent to: ' . $reminder->user->email);
            } catch (\Exception $e) {
                Log::error('Error sending reminder to ' . $reminder->user->email . ': ' . $e->getMessage());
            }
        }
    }
}
