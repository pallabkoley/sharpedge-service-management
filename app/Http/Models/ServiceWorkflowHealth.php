<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceWorkflowHealth extends Model
{
    protected $table = 'service_workflow_health';

    protected $fillable = [
        'order_id',
        'service_id',
        'work_process_id',

        'total_tasks',
        'verified_tasks',
        'pending_tasks',
        'rejected_tasks',

        'order_ok',
        'health_status',
        'last_evaluated_at',
    ];

    /*
    |--------------------------------------------------------------------------
    | CASTS
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'order_ok' => 'boolean',

        'total_tasks' => 'integer',
        'verified_tasks' => 'integer',
        'pending_tasks' => 'integer',
        'rejected_tasks' => 'integer',

        'last_evaluated_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function workProcess()
    {
        return $this->belongsTo(WorkProcess::class, 'work_process_id');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeReady($query)
    {
        return $query->where('health_status', 'READY');
    }

    public function scopeAttention($query)
    {
        return $query->where('health_status', 'ATTENTION');
    }

    public function scopeNotReady($query)
    {
        return $query->where('health_status', 'NOT_READY');
    }

    /*
    |--------------------------------------------------------------------------
    | STATUS HELPERS
    |--------------------------------------------------------------------------
    */

    public function isReady(): bool
    {
        return $this->health_status === 'READY';
    }

    public function isNotReady(): bool
    {
        return $this->health_status === 'NOT_READY';
    }

    public function needsAttention(): bool
    {
        return $this->health_status === 'ATTENTION';
    }

    /*
    |--------------------------------------------------------------------------
    | WORKFLOW PROGRESS
    |--------------------------------------------------------------------------
    */

    public function progressPercent(): int
    {
        if ($this->total_tasks <= 0) {
            return 0;
        }

        return (int) round(($this->verified_tasks / $this->total_tasks) * 100);
    }

    /*
    |--------------------------------------------------------------------------
    | WORKFLOW COMPLETION
    |--------------------------------------------------------------------------
    */

    public function isWorkflowCompleted(): bool
    {
        return (
            $this->total_tasks > 0 &&
            $this->verified_tasks === $this->total_tasks &&
            $this->rejected_tasks === 0 &&
            $this->order_ok === true
        );
    }

    /*
    |--------------------------------------------------------------------------
    | CERTIFICATE ELIGIBILITY
    |--------------------------------------------------------------------------
    */

    public function canUploadCertificate(): bool
    {
        return (
            $this->isWorkflowCompleted() &&
            $this->health_status === 'READY'
        );
    }
}