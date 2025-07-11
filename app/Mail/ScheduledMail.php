<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ScheduledMail extends Mailable
{
    use Queueable, SerializesModels;

    public $project;
    public $user;
    public $isCEO;

    public function __construct($project, $user, $isCEO)
    {
        $this->project = $project;
        $this->user = $user;
        $this->isCEO = $isCEO;
    }

    public function build()
    {
        return $this->subject('⏰ Reminder Proyek: ' . $this->project->name)
                    ->view('mail.scheduled')
                    ->with([
                        'project' => $this->project,
                        'user' => $this->user,
                        'isCEO' => $this->isCEO,
                    ]);
    }
}
