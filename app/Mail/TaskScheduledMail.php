<?php
namespace App\Mail;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class TaskScheduledMail extends Mailable
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
        Log::info($this->task);
        Log::info($user);
    }

    public function build()
    {
        
        $subject = '⏰ Reminder Proyek: ' . $this->task->title;

        if (!empty($this->task->due_date)) {
            try {
                $subject .= ' (Selesai: ' . $this->task->due_date->format('d-m-Y') . ')';
            } catch (\Exception $e) {
                Log::warning('Gagal parse due_date:', ['due_date' => $this->task->due_date]);
            }
        }


        return $this->subject($subject)
                    ->view('mail.task_reminder')
                    ->with([
                        'task' => $this->task,
                        'user' => $this->user,
                        'isCEO' => $this->isCEO,
                    ]);
    }
}
