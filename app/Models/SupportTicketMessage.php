<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportTicketMessage extends Model
{
    protected $table = 'support_ticket_messages';

    /* =====================
       Mass Assignment
    ===================== */
    protected $fillable = [
        'support_ticket_id',
        'user_id',
        'user_role',
        'message',
        'is_internal',
    ];

    /* =====================
       Casting
    ===================== */
    protected $casts = [
        'is_internal' => 'boolean',
    ];

    /* =====================
       Relationships
    ===================== */

    // Message belongs to a ticket
    public function ticket()
    {
        return $this->belongsTo(SupportTicket::class, 'support_ticket_id');
    }

    // Message written by a user (admin / staff / client)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /* =====================
       Visibility Helpers
    ===================== */

    // Can client see this message?
    public function visibleToClient(): bool
    {
        return $this->is_internal === false;
    }

    // Is this an internal staff note?
    public function isInternal(): bool
    {
        return $this->is_internal === true;
    }

    /* =====================
       Role Helpers
    ===================== */

    public function writtenByAdmin(): bool
    {
        return $this->user_role === 'admin';
    }

    public function writtenByStaff(): bool
    {
        return in_array($this->user_role, ['backoffice', 'marketing']);
    }

    public function writtenByClient(): bool
    {
        return $this->user_role === 'client';
    }
}