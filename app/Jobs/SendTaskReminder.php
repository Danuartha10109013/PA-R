<?php
namespace App\Jobs;

use App\Models\NotificationM;
use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendTaskReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    public function handle()
    {
        // Pastikan task masih valid
        if (!$this->task || !$this->task->due_date) {
            return;
        }
        $tsk = Task::find($this->task->id);

        // Simpan notifikasi reminder
        NotificationM::create([
            'title' => 'Reminder: Deadline Task "' . $this->task->title . '" besok!',
            'content' => 'Task "' . $this->task->title . '" akan jatuh tempo besok (' . $this->task->due_date . ').',
            'status_ceo' => 0,
            'status_marketing' => 0,
            'user_id' => $this->task->user_id,
            'projects_id' => $tsk->project_id,
            'tasks_id' => $this->task->id,
        ]);
    }
}
