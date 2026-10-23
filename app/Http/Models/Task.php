<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Order;
use App\Models\Service;
use App\Models\WorkProcess;
use App\Models\WorkProcessStep;
use App\Models\Staff;
use App\Models\User;
use App\Models\TaskDocument;
use App\Models\TaskMessage;

use App\Http\Controllers\Admin\ServiceWorkflowHealthController;

class Task extends Model
{
    use HasFactory;

    protected $table = 'tasks';

    /*
    |--------------------------------------------------------------------------
    | FILLABLE
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'task_code',
        'order_id',
        'service_id',
        'work_process_id',
        'work_process_step_id',
        'task_name',
        'step_name',
        'assigned_to',
        'is_new_for_staff',
        'priority',
        'due_date',
        'started_at',
        'submitted_at',
        'verified_at',
        'verified_by',
        'rejected_at',
        'rejected_by',
        'status',
        'remarks',
        'admin_notes',
        'is_renewal',
        'renewal_no',
        'parent_cycle_task_id',
    ];

    /*
    |--------------------------------------------------------------------------
    | CASTS
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'due_date' => 'date',
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'verified_at' => 'datetime',
        'rejected_at' => 'datetime',

        'is_new_for_staff' => 'boolean',
        'is_renewal' => 'boolean',
        'renewal_no' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | DEFAULT VALUES
    |--------------------------------------------------------------------------
    */

    protected $attributes = [
        'status' => 'unassigned',
        'is_new_for_staff' => 0,
        'is_renewal' => 0,
        'renewal_no' => 0,
    ];

    /*
    |--------------------------------------------------------------------------
    | BOOT METHOD
    |--------------------------------------------------------------------------
    */

    protected static function boot()
    {
        parent::boot();

        /*
        |--------------------------------------------------------------------------
        | AUTO GENERATE TASK CODE
        |--------------------------------------------------------------------------
        */

        static::creating(function ($task) {

            if (empty($task->task_code)) {

                $nextId = (static::max('id') ?? 0) + 1;

                $task->task_code = 'TASK-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
            }

            $task->is_renewal = $task->is_renewal ?? 0;
            $task->renewal_no = $task->renewal_no ?? 0;
            $task->is_new_for_staff = $task->is_new_for_staff ?? 0;
        });

        /*
        |--------------------------------------------------------------------------
        | WORKFLOW HEALTH AUTO UPDATE
        |--------------------------------------------------------------------------
        */

        static::created(function ($task) {

            try {

                app(ServiceWorkflowHealthController::class)
                    ->calculateWorkflowHealth($task->order_id);

            } catch (\Throwable $e) {

                \Log::error('Workflow Health Recalculation Failed', [
                    'order_id' => $task->order_id,
                    'error' => $e->getMessage()
                ]);
            }
        });

        static::updated(function ($task) {

            if ($task->isDirty('status')) {

                try {

                    app(ServiceWorkflowHealthController::class)
                        ->calculateWorkflowHealth($task->order_id);

                } catch (\Throwable $e) {

                    \Log::error('Workflow Health Recalculation Failed', [
                        'order_id' => $task->order_id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        });

        static::deleted(function ($task) {

            try {

                app(ServiceWorkflowHealthController::class)
                    ->calculateWorkflowHealth($task->order_id);

            } catch (\Throwable $e) {

                \Log::error('Workflow Health Recalculation Failed', [
                    'order_id' => $task->order_id,
                    'error' => $e->getMessage()
                ]);
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function workProcess()
    {
        return $this->belongsTo(WorkProcess::class);
    }

    public function workProcessStep()
    {
        return $this->belongsTo(WorkProcessStep::class);
    }

    public function assignedStaff()
    {
        return $this->belongsTo(Staff::class, 'assigned_to');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function parentCycleTask()
    {
        return $this->belongsTo(Task::class, 'parent_cycle_task_id');
    }

    public function renewalTasks()
    {
        return $this->hasMany(Task::class, 'parent_cycle_task_id');
    }

    /*
    |--------------------------------------------------------------------------
    | DOCUMENTS & MESSAGES
    |--------------------------------------------------------------------------
    */

    public function documents()
    {
        return $this->hasMany(TaskDocument::class);
    }

    public function messages()
    {
        return $this->hasMany(TaskMessage::class)->latest();
    }

    /*
    |--------------------------------------------------------------------------
    | QUERY SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeAssignedTo($query, $staffId)
    {
        return $query->where('assigned_to', $staffId);
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['verified']);
    }

    public function scopeOverdue($query)
    {
        return $query
            ->whereDate('due_date', '<', now())
            ->whereIn('status', ['assigned', 'in_progress', 'waiting_client']);
    }

    public function scopeRejected($query)
    {
        return $query->whereNotNull('rejected_at');
    }

    public function scopeSubmitted($query)
    {
        return $query
            ->where('status', 'submitted')
            ->whereNotNull('submitted_at');
    }

    /*
    |--------------------------------------------------------------------------
    | STATUS HELPERS
    |--------------------------------------------------------------------------
    */

    public function isSubmitted()
    {
        return $this->status === 'submitted';
    }

    public function isVerified()
    {
        return $this->status === 'verified';
    }

    public function isRejected()
    {
        return !is_null($this->rejected_at);
    }

    public function isNewForStaff()
    {
        return (bool) $this->is_new_for_staff;
    }

    /*
    |--------------------------------------------------------------------------
    | RENEWAL HELPERS
    |--------------------------------------------------------------------------
    */

    public function isRenewal()
    {
        return (bool) $this->is_renewal;
    }

    public function renewalLabel()
    {
        return $this->renewal_no > 0
            ? 'Renewal #' . $this->renewal_no
            : 'New';
    }
}