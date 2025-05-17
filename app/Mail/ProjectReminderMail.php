<?php

namespace App\Mail;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use App\Models\ReminderProject;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProjectReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $reminder;

    public function __construct(ReminderProject $reminder)
    {
        $this->reminder = $reminder;
    }

    public function build()
    {
        $project = $this->reminder->project;

        return $this->subject('⏰ Reminder: Deadline Proyek ' . $project->name . ' Besok!')
            ->view('mail.project_reminder', [
                'project' => $project,
                'user' => $this->reminder->user,
                'deadline' => $project->end_date->format('d F Y')
            ]);
    }
}
