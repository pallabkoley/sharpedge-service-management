<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskDocument extends Model
{
    use HasFactory;

    protected $table = 'task_documents';

    protected $fillable = [
        'task_id',
        'uploaded_by',
        'document_name',
        'file_path',
        'file_type',
        'file_size',
        'status',        // uploaded | submitted | verified | rejected
        'remarks',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
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

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
