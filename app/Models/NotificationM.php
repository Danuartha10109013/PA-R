<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationM extends Model
{
    use HasFactory;

    protected $table = 'notification';

    protected $fillable = [
        'user_id',
        'projects_id',
        'tasks_id',
        'title',
        'content',
        'status_marketing',
        'status_ceo',
    ];
}
