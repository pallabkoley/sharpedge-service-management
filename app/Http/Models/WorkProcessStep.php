<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkProcessStep extends Model
{
    use HasFactory;

    protected $table = 'work_process_steps';

    protected $fillable = [
        'work_process_id',
        'step_name',
        'step_order',
        'estimated_days',
        'client_instruction', // only this instruction
    ];

    /**
     * Parent work process
     */
    public function workProcess()
    {
        return $this->belongsTo(WorkProcess::class);
    }

    /**
     * Tasks related to this step
     */
    public function tasks()
    {
        return $this->hasMany(Task::class, 'work_process_step_id');
    }
}
