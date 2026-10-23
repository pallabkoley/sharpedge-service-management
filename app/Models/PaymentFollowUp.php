<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentFollowUp extends Model
{
    use HasFactory;

    protected $table = 'payment_follow_ups';

    /* =========================
       MASS ASSIGNMENT
    ========================== */

    protected $fillable = [
        'payment_id',
        'order_id',
        'follow_up_date',
        'next_follow_up_date',
        'note',
        'created_by',
    ];

    /* =========================
       CASTS
    ========================== */

    protected $casts = [
        'follow_up_date'      => 'date',
        'next_follow_up_date' => 'date',
    ];

    /* =========================
       RELATIONSHIPS
    ========================== */

    /**
     * Payment associated with this follow-up.
     */
    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }

    /**
     * Order associated with this follow-up.
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    /**
     * User who created the follow-up.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /* =========================
       ACCESSORS
    ========================== */

    /**
     * Formatted follow-up date.
     */
    public function getFormattedFollowUpDateAttribute(): ?string
    {
        return $this->follow_up_date
            ? $this->follow_up_date->format('d M Y')
            : null;
    }

    /**
     * Formatted next follow-up date.
     */
    public function getFormattedNextFollowUpDateAttribute(): ?string
    {
        return $this->next_follow_up_date
            ? $this->next_follow_up_date->format('d M Y')
            : null;
    }
}