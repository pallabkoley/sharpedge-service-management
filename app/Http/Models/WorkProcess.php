<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkProcess extends Model
{
    use HasFactory;

    protected $table = 'work_processes';

    protected $fillable = [
        'service_id',
        'name',
        'status',
        'created_by',
    ];

    /**
     * Related service (GST, ITR, Trade License, etc.)
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Steps of this work process
     */
public function steps()
{
    return $this->hasMany(WorkProcessStep::class)
                ->orderBy('step_order');
}
public function tasks()
{
    return $this->hasMany(Task::class);
}


    /**
     * Admin/Staff who created this process
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
