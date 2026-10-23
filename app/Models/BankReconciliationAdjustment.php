<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankReconciliationAdjustment extends Model
{
    use HasFactory;

    protected $table = 'bank_reconciliation_adjustments';

    protected $fillable = [
        'bank_reconciliation_id',
        'adjustment_date',
        'adjustment_type',
        'amount',
        'remarks',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'adjustment_date' => 'date',
        'amount' => 'decimal:2',
        'status' => 'boolean',
    ];

    public function bankReconciliation()
    {
        return $this->belongsTo(BankReconciliation::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status',1);
    }
}