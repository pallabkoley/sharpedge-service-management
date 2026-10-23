<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankReconciliation extends Model
{
    use HasFactory;

    protected $table = 'bank_reconciliations';

    protected $fillable = [
        'computation_id',
        'statement_date',
        'closing_balance_bank',
        'closing_balance_cashbook',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'statement_date' => 'date',
        'closing_balance_bank' => 'decimal:2',
        'closing_balance_cashbook' => 'decimal:2',
        'status' => 'boolean',
    ];

    protected $appends = [
        'outstanding_cheques',
        'outstanding_deposits',
        'other_adjustments',
        'adjusted_balance',
        'variance',
    ];

    public function computation()
    {
        return $this->belongsTo(Computation::class);
    }

    public function cheques()
    {
        return $this->hasMany(BankReconciliationCheque::class);
    }

    public function deposits()
    {
        return $this->hasMany(BankReconciliationDeposit::class);
    }

    public function adjustments()
    {
        return $this->hasMany(BankReconciliationAdjustment::class);
    }

    public function getOutstandingChequesAttribute()
    {
        return $this->cheques()->sum('amount');
    }

    public function getOutstandingDepositsAttribute()
    {
        return $this->deposits()->sum('amount');
    }

    public function getOtherAdjustmentsAttribute()
    {
        return $this->adjustments()->sum('amount');
    }

    public function getAdjustedBalanceAttribute()
    {
        return
            $this->closing_balance_bank
            - $this->outstanding_cheques
            + $this->outstanding_deposits
            + $this->other_adjustments;
    }

    public function getVarianceAttribute()
    {
        return
            $this->adjusted_balance
            - $this->closing_balance_cashbook;
    }

    public function scopeActive($query)
    {
        return $query->where('status',1);
    }
}