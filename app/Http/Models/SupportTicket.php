<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class SupportTicket extends Model
{
    use SoftDeletes;

    protected $table = 'support_tickets';

    /* =====================
       Mass Assignment
    ===================== */
    protected $fillable = [
        'ticket_no',
        'client_id',
        'order_id',
        'created_by',
        'created_by_role',
        'assigned_to',
        'assigned_by',
        'title',
        'description',
        'pending_reason',
        'ticket_type',
        'status',
        'client_confirmation',
        'priority',
        'sla_hours',
        'due_at',
    ];

    /* =====================
       Casting
    ===================== */
    protected $casts = [
        'due_at' => 'datetime',
    ];

    /* =====================
       Relationships
    ===================== */

    // Ticket belongs to a client
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // Ticket belongs to an order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Ticket assigned to a user (Admin / Backoffice / Marketing)
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // Admin who assigned the ticket
    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    // User who created the ticket
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /* =====================
       Status Helpers
    ===================== */

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isAssigned(): bool
    {
        return $this->status === 'assigned';
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isResolved(): bool
    {
        return $this->status === 'resolved';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    public function isReopened(): bool
    {
        return $this->status === 'reopened';
    }

    /* =====================
       SLA & Time Helpers
    ===================== */

    public function isOverdue(): bool
    {
        if ($this->status === 'pending') {
            return false;
        }

        return $this->due_at && now()->greaterThan($this->due_at);
    }

    public function remainingTime(): ?string
    {
        if (!$this->due_at) {
            return null;
        }

        if ($this->isOverdue()) {
            return 'Overdue';
        }

        return now()->diffForHumans($this->due_at, [
            'parts'  => 2,
            'short'  => true,
            'syntax' => Carbon::DIFF_ABSOLUTE,
        ]);
    }

    /* =====================
       Client Confirmation Helpers
    ===================== */

    public function canBeClosed(): bool
    {
        return $this->status === 'resolved'
            && $this->client_confirmation === 'accepted';
    }

    public function canBeReopened(): bool
    {
        return $this->client_confirmation === 'rejected';
    }

    /* =====================
       Ticket Number Generator
    ===================== */
    
    public function messages()
{
    return $this->hasMany(
        SupportTicketMessage::class,
        'support_ticket_id'
    )->orderBy('created_at', 'asc');
}

    public static function generateTicketNo(): string
    {
        $lastId = self::withTrashed()->max('id') ?? 0;

        return 'TKT-' . str_pad($lastId + 1, 5, '0', STR_PAD_LEFT);
    }
}