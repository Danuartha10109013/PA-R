<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'user_id',
        'type',
        'message',
        'sent_at',
        'is_sent'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // app/Models/Notification.php

    public static function createReminder(Project $project, User $user, $daysBefore = 1)
    {
        return self::create([
            'project_id' => $project->id,
            'user_id' => $user->id,
            'type' => 'reminder',
            'message' => "Reminder: Project {$project->name} dimulai dalam {$daysBefore} hari",
            'scheduled_at' => now(),
            'is_sent' => false
        ]);
    }
}
