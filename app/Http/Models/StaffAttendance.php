<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffAttendance extends Model
{
    use HasFactory;

    protected $table = 'staff_attendances';

    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'staff_id',
        'attendance_date',
        'clock_in_at',
        'clock_out_at',
        'status',
    ];

    /**
     * Cast attributes
     */
    protected $casts = [
        'attendance_date' => 'date',
        'clock_in_at'     => 'datetime',
        'clock_out_at'    => 'datetime',
    ];

    /**
     * Relationship: Attendance belongs to a staff
     */
    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    /* =========================
       Helper methods (optional)
    ========================== */

    /**
     * Check if staff has clocked in
     */
    public function isClockedIn()
    {
        return !is_null($this->clock_in_at);
    }

    /**
     * Check if staff has clocked out
     */
    public function isClockedOut()
    {
        return !is_null($this->clock_out_at);
    }

    /**
     * Calculate working duration in minutes
     */
    public function workingMinutes()
    {
        if ($this->clock_in_at && $this->clock_out_at) {
            return $this->clock_out_at->diffInMinutes($this->clock_in_at);
        }

        return 0;
    }
}
