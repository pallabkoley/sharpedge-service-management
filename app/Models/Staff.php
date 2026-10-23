<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Staff extends Model
{
    use HasFactory;

    protected $table = 'staffs';

    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'staff_code',
        'user_id',
        'role_id',
        'name',
        'email',
        'phone',
        'profile_image',
        'department',
        'designation',
        'date_joined',
        'status',
        'is_online',
        'last_seen_at',
        'created_by',
        'updated_by',
    ];

    /**
     * Attribute casting
     */
    protected $casts = [
        'date_joined'  => 'date',
        'last_seen_at' => 'datetime',
        'is_online'    => 'boolean',
    ];

    /**
     * Auto-generate staff_code
     * Example: STF00001
     */
     
     public function getProfileImageUrlAttribute()
{
    return $this->profile_image && Storage::disk('public')->exists($this->profile_image)
        ? Storage::url($this->profile_image)
        : asset('assets/img/default-user.png');
}
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($staff) {
            if (empty($staff->staff_code)) {
                $nextId = (self::max('id') ?? 0) + 1;
                $staff->staff_code = 'STF' . str_pad($nextId, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    /* =========================
       Relationships
    ========================== */
    
    
    public function tasks()
{
    return $this->hasMany(Task::class, 'assigned_to');
}


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function orderStatusChanges()
{
    return $this->hasMany(OrderStatusLog::class, 'changed_by');
}

public function attendanceLogs()
{
    return $this->hasMany(AttendanceLog::class, 'staff_id');
}

public function leaveRequests()
{
    return $this->hasMany(\App\Models\LeaveRequest::class, 'staff_id');
}


}
