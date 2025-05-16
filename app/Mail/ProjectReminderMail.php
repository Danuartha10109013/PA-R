<?php

namespace App\Mail;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProjectReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $project;
    public $daysLeft;

    public function __construct(Project $project, $daysLeft = 1)
    {
        $this->project = $project;
        $this->daysLeft = $daysLeft;
    }

    public function build()
    {
        return $this->subject('Reminder: Project ' . $this->project->name . ' Dimulai Besok')
            ->view('emails.project_reminder')
            ->with([
                'project' => $this->project,
                'daysLeft' => $this->daysLeft
            ]);
    }
}
