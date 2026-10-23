<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceLog extends Model
{
    use HasFactory;

    protected $table = 'attendance_logs';

    protected $fillable = [
        'user_id',
        'staff_id',
        'date',
        'clock_in_time',
        'clock_out_time',
        'total_work_seconds',
        'auto_clock_out',
    ];

    protected $casts = [
        'date' => 'date',
        'auto_clock_out' => 'boolean',
        'total_work_seconds' => 'integer',
    ];

    /* =========================
       Relationships
    ========================== */

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }
}
