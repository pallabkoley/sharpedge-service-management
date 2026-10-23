<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskMessage extends Model
{
    use HasFactory;

    protected $table = 'task_messages';

    protected $fillable = [
        'task_id',
        'sender_id',
        'sender_role',   // backoffice | admin | client
        'message',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    /**
     * =========================
     * RELATIONSHIPS
     * =========================
     */

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
