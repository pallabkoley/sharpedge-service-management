<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeavePolicy extends Model
{
    use HasFactory;

    protected $table = 'leave_policies';

    protected $fillable = [
        'leave_type_id',
        'max_per_year',
        'max_per_month',
    ];

    // Policy belongs to one Leave Type
    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
    }
}
