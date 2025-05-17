<?php

namespace App\Mail;

use App\Models\Project;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProjectReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $project;
    public $user;
    public $roleType;

    public function __construct(Project $project, User $user, $roleType)
    {
        $this->project = $project;
        $this->user = $user;
        $this->roleType = $roleType;
    }

    public function build()
    {
        $subject = $this->roleType === 'CEO'
            ? "🚨 [Prioritas CEO] Deadline Proyek {$this->project->name} Besok!"
            : "⏰ Reminder: Deadline Proyek {$this->project->name} Besok";

        return $this->subject($subject)
            ->view('mail.project_reminder', [
                'deadline' => $this->project->end_date->format('d F Y'),
                'isCEO' => $this->roleType === 'CEO'
            ]);
    }
}
