<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    use HasFactory;

    protected $table = 'leave_requests';

    protected $fillable = [
        'user_id',
        'staff_id',
        'leave_type_id',
        'from_date',
        'to_date',
        'total_days',
        'is_half_day',
        'half_day_type',
        'reason',
        'attachment',
        'status',
        'approved_by',
        'approved_at',
        'rejection_reason',
    ];

    protected $casts = [
        'from_date' => 'date',
        'to_date' => 'date',
        'approved_at' => 'datetime',
        'is_half_day' => 'boolean',
        'total_days' => 'decimal:2',
    ];

    // Leave request belongs to one Leave Type
    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
    }

    // Leave request belongs to one User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Leave request belongs to one Staff
    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    // Approved by (Admin User)
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
