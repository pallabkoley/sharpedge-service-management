<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    use HasFactory;

    protected $table = 'leave_types';

    protected $fillable = [
        'name',
        'code',
        'status',
    ];
    public function approvedRequests()
{
    return $this->hasMany(LeaveRequest::class, 'leave_type_id')
        ->where('status', 'approved');
}


    // One Leave Type has One Policy
    public function policy()
    {
        return $this->hasOne(LeavePolicy::class, 'leave_type_id');
    }

    // One Leave Type has Many Leave Requests
    public function requests()
    {
        return $this->hasMany(LeaveRequest::class, 'leave_type_id');
    }
}
