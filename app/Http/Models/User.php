<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'role_id',
        'name',
        'email',
        'password',
        'phone',
        'address',
        'profile_image',
        'status',
        'last_login',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login'        => 'datetime',
    ];

    /* =========================
       CORE RELATIONSHIPS
    ========================= */

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function staff()
    {
        return $this->hasOne(Staff::class, 'user_id');
    }

    public function client()
    {
        return $this->hasOne(Client::class, 'user_id');
    }

    public function clientProfile()
    {
        return $this->hasOne(ClientProfile::class);
    }

    /* =========================
       BUSINESS RELATIONSHIPS
    ========================= */

    public function serviceRequests()
    {
        return $this->hasMany(ServiceRequest::class, 'client_id');
    }

    public function assignedLeads()
    {
        return $this->hasMany(Lead::class, 'assigned_to');
    }

    public function createdServices()
    {
        return $this->hasMany(Service::class, 'created_by');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    /* =========================
       MEETING RELATIONSHIPS
    ========================= */

    public function requestedMeetings()
    {
        return $this->hasMany(Meeting::class, 'requested_by');
    }

    public function assignedMeetings()
    {
        return $this->hasMany(Meeting::class, 'assigned_to');
    }

    public function createdMeetingAvailabilities()
    {
        return $this->hasMany(MeetingAvailability::class, 'created_by');
    }

    public function meetingLogs()
    {
        return $this->hasMany(MeetingLog::class, 'performed_by');
    }

    /* =========================
       ADMIN / HR
    ========================= */

    public function adminSetting()
    {
        return $this->hasOne(AdminSetting::class, 'admin_id');
    }

    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class, 'user_id');
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class, 'user_id');
    }

    public function approvedLeaveRequests()
    {
        return $this->hasMany(LeaveRequest::class, 'approved_by');
    }

    /* =========================
       SCOPES
    ========================= */

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeAdmin($query)
    {
        return $query->whereHas('role', fn ($q) => $q->where('name', 'admin'));
    }

    public function scopeBackOffice($query)
    {
        return $query->whereHas('role', fn ($q) => $q->where('name', 'backoffice'));
    }

    public function scopeMarketing($query)
    {
        return $query->whereHas('role', fn ($q) => $q->where('name', 'marketing'));
    }

    public function scopeClient($query)
    {
        return $query->whereHas('role', fn ($q) => $q->where('name', 'client'));
    }
}