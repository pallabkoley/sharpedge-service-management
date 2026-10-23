<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankReconciliationCheque extends Model
{
    use HasFactory;

    protected $table = 'bank_reconciliation_cheques';

    protected $fillable = [
        'bank_reconciliation_id',
        'cheque_date',
        'cheque_no',
        'amount',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'cheque_date' => 'date',
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