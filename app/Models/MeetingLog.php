<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeetingLog extends Model
{
    use HasFactory;

    // DB handles created_at
    public $timestamps = false;

    protected $fillable = [
        'meeting_id',
        'action',
        'performed_by',
        'source',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'action'     => 'string',
        'source'     => 'string',
    ];

    /* =========================
       RELATIONSHIPS
    ========================= */

    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }

    public function performer()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}