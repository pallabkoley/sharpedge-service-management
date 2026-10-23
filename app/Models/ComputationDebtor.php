<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComputationDebtor extends Model
{
    protected $table = 'computation_debtors';

    protected $fillable = [
        'computation_id',
        'debtor_name',
        'pan',
        'gstin',
        'bill_amount',
        'payment_followup_date',
        'amount_received',
        'outstanding_amount',
        'remarks',
        'sort_order',
    ];

    protected $casts = [
        'bill_amount' => 'decimal:2',
        'amount_received' => 'decimal:2',
        'outstanding_amount' => 'decimal:2',
        'payment_followup_date' => 'date',
        'sort_order' => 'integer',
    ];

    /**
     * Parent Computation
     */
    public function computation()
    {
        return $this->belongsTo(Computation::class, 'computation_id');
    }

    /**
     * Automatically calculate outstanding amount.
     */
    protected static function booted()
    {
        static::saving(function ($debtor) {
            $debtor->outstanding_amount =
                ($debtor->bill_amount ?? 0) -
                ($debtor->amount_received ?? 0);
        });
    }
}