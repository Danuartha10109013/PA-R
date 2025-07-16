<?php

namespace App\Jobs;

use App\Models\NotificationM;
use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendPjctRm implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $projectId;

    public function __construct($projectId)
    {
        $this->projectId = $projectId;
    }

public function handle()
{
    $project = Project::find($this->projectId);

     if (!$project || !$project->end_date) {
        Log::warning("Project ID {$this->projectId} tidak ditemukan atau end_date kosong.");
        return;
    }

    try {
        NotificationM::create([
            'title' => 'Reminder: Deadline project "' . $project->name . '" besok!',
            'content' => 'Project "' . $project->name . '" akan jatuh tempo besok (' . $project->end_date . ').',
            'status_ceo' => 0,
            'status_marketing' => 0,
            'user_id' => $project->user_id,
            'projects_id' => $project->id,
            'tasks_id' => null,
        ]);
        Log::info("Reminder berhasil dibuat untuk project ID: {$project->id}");
    } catch (\Exception $e) {
        Log::error("Gagal membuat reminder: " . $e->getMessage());
    }
}
}
