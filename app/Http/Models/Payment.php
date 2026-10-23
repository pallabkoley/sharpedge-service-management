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

        // payment info
        'payment_method',
        'transaction_id',
        'payment_proof_path',
        'amount',
        'payment_date',

        // staff tracking
        'received_by',
        'approved_by',

        // status tracking
        'approved_at',
        'rejected_at',
        'status',

        // extra
        'remarks',
        'rejection_reason',
    ];

    /* =========================
       CASTS
    ========================== */

    protected $casts = [
        'amount'        => 'decimal:2',
        'payment_date'  => 'date',
        'approved_at'   => 'datetime',
        'rejected_at'   => 'datetime',
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

    /* =========================
       SCOPE (VERY USEFUL 🔥)
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

    /* =========================
       ACCESSORS (OPTIONAL)
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
}