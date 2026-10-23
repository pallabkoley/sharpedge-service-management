<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DebtorReconciliation extends Model
{
    use HasFactory;

    protected $table = 'debtor_reconciliations';

    protected $fillable = [
        'computation_id',
        'debtor_name',
        'pan',
        'gstin',
        'bill_amount',
        'payment_followup_date',
        'amount_received',
        'remarks',
        'sort_order',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'bill_amount' => 'decimal:2',
        'amount_received' => 'decimal:2',
        'payment_followup_date' => 'date',
        'status' => 'boolean',
    ];

    protected $appends = [
        'outstanding',
    ];

    /**
     * Outstanding Amount
     */
    public function getOutstandingAttribute()
    {
        return (float) $this->bill_amount - (float) $this->amount_received;
    }

    /**
     * Parent Computation
     */
    public function computation()
    {
        return $this->belongsTo(Computation::class, 'computation_id');
    }

    /**
     * Creator
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Updater
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope: Active Records
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}