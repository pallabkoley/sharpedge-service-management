<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeetingAvailability extends Model
{
    use HasFactory;

    protected $table = 'meeting_availabilities';

    // created_at handled by DB
    public $timestamps = false;

    protected $fillable = [
        'meeting_id',
        'available_date',
        'start_time',
        'end_time',
        'created_by',
    ];

    protected $casts = [
        'available_date' => 'date:Y-m-d',
        'start_time'     => 'datetime:H:i',
        'end_time'       => 'datetime:H:i',
        'created_at'     => 'datetime',
    ];

    /* =========================
       RELATIONSHIPS
    ========================= */

    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}