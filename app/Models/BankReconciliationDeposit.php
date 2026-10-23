<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankReconciliationDeposit extends Model
{
    use HasFactory;

    protected $table = 'bank_reconciliation_deposits';

    protected $fillable = [
        'bank_reconciliation_id',
        'deposit_date',
        'amount',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'deposit_date' => 'date',
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