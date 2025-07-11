<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ImmediateMail extends Mailable
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
        return $this->subject('📢 Proyek Baru: ' . $this->project->name)
                    ->view('mail.immediate')
                    ->with([
                        'project' => $this->project,
                        'user' => $this->user,
                        'isCEO' => $this->isCEO,
                    ]);
    }
}
