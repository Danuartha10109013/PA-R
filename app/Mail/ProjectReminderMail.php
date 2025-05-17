<?php

namespace App\Mail;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use App\Models\ReminderProject;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProjectReminderMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $reminder;

    public function __construct(ReminderProject $reminder)
    {
        $this->reminder = $reminder;
    }

    public function build()
    {
        return $this->subject('Reminder: Project ' . $this->reminder->project->name . ' will start tomorrow')
            ->view('mail.project_reminder');
    }
}
