<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments';

    /* =========================
       MASS ASSIGNMENT
    ========================== */

    protected $fillable = [
        'order_id',
        'client_id',

        // Payment info
        'payment_method',
        'transaction_id',
        'payment_proof_path',
        'amount',
        'payment_date',

        // Staff tracking
        'received_by',
        'approved_by',

        // Status tracking
        'approved_at',
        'rejected_at',
        'status',

        // Pay Later tracking
        'payment_received',
        'paid_at',
        'paid_by',

        // Extra
        'remarks',
        'rejection_reason',
    ];

    /* =========================
       CASTS
    ========================== */

    protected $casts = [
        'amount'           => 'decimal:2',
        'payment_date'     => 'date',
        'approved_at'      => 'datetime',
        'rejected_at'      => 'datetime',
        'payment_received' => 'boolean',
        'paid_at'          => 'datetime',
        'paid_by'          => 'integer',
    ];

    /* =========================
       RELATIONSHIPS
    ========================== */

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function receivedByStaff()
    {
        return $this->belongsTo(Staff::class, 'received_by');
    }

    public function approvedByStaff()
    {
        return $this->belongsTo(Staff::class, 'approved_by');
    }

    /**
     * User who marked the Pay Later payment as paid.
     */
    public function paidBy()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    /**
     * Follow-up history for this payment.
     */
    public function followUps()
    {
        return $this->hasMany(PaymentFollowUp::class, 'payment_id');
    }

    /* =========================
       STATUS HELPERS
    ========================== */

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isPending(): bool
    {
        return $this->status === 'submitted';
    }

    /**
     * Check whether this payment uses Pay Later.
     */
    public function isPayLater(): bool
    {
        return strtoupper((string) $this->payment_method) === 'PAY_LATER';
    }

    /**
     * Check whether the Pay Later payment has actually been received.
     */
    public function isPaymentReceived(): bool
    {
        return (bool) $this->payment_received;
    }

    /**
     * Check whether Pay Later payment is still pending.
     */
    public function isPaymentPending(): bool
    {
        return $this->isPayLater() && !$this->isPaymentReceived();
    }

    /* =========================
       SCOPES
    ========================== */

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'submitted');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Get Pay Later payments.
     */
    public function scopePayLater($query)
    {
        return $query->whereRaw('UPPER(payment_method) = ?', ['PAY_LATER']);
    }

    /**
     * Get Pay Later payments whose actual payment has not been received.
     */
    public function scopePayLaterPending($query)
    {
        return $query
            ->whereRaw('UPPER(payment_method) = ?', ['PAY_LATER'])
            ->where('payment_received', false);
    }

    /**
     * Get Pay Later payments that have been received.
     */
    public function scopePayLaterPaid($query)
    {
        return $query
            ->whereRaw('UPPER(payment_method) = ?', ['PAY_LATER'])
            ->where('payment_received', true);
    }

    /* =========================
       ACCESSORS
    ========================== */

    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 2);
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'approved' => 'success',
            'rejected' => 'danger',
            default    => 'warning',
        };
    }

    /**
     * Payment Tracking status for Pay Later.
     *
     * This is separate from the existing approval status.
     */
    public function getPaymentTrackingStatusAttribute(): string
    {
        if ($this->isPayLater()) {
            return $this->isPaymentReceived()
                ? 'paid'
                : 'pending';
        }

        return $this->status;
    }

    /**
     * Bootstrap-style badge for Payment Tracking.
     */
    public function getPaymentTrackingBadgeAttribute(): string
    {
        if ($this->isPayLater()) {
            return $this->isPaymentReceived()
                ? 'success'
                : 'warning';
        }

        return $this->statusBadge;
    }
}