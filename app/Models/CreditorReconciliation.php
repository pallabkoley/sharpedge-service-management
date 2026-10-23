<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditorReconciliation extends Model
{
    use HasFactory;

    protected $table = 'creditor_reconciliations';

    protected $fillable = [
        'computation_id',
        'creditor_name',
        'pan',
        'gstin',
        'bill_amount',
        'payment_followup_date',
        'amount_paid',
        'remarks',
        'sort_order',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'bill_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'payment_followup_date' => 'date',
        'status' => 'boolean',
    ];

    protected $appends = [
        'outstanding_payable',
    ];

    /**
     * Outstanding Payable
     */
    public function getOutstandingPayableAttribute()
    {
        return (float) $this->bill_amount - (float) $this->amount_paid;
    }

    /**
     * Parent Computation
     */
    public function computation()
    {
        return $this->belongsTo(Computation::class, 'computation_id');
    }

    /**
     * Created By
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Updated By
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Active Records Scope
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}