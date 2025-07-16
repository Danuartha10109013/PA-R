<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TaskNewMail extends Mailable
{
    use Queueable, SerializesModels;

    public $task;
    public $user;
    public $isCEO;

    public function __construct($task, $user, $isCEO)
    {
        $this->task = $task;
        $this->user = $user;
        $this->isCEO = $isCEO;
    }

    public function build()
    {
        return $this->subject('📢 Task Baru: ' . $this->task->title)
                    ->view('mail.newtask')
                    ->with([
                        'task' => $this->task,
                        'user' => $this->user,
                        'isCEO' => $this->isCEO,
                    ]);
    }
}
