<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TradingAccount extends Model
{
    protected $fillable = [
        'computation_id',
        'adjustment_type',
    ];

    public function computation()
    {
        return $this->belongsTo(Computation::class, 'computation_id');
    }

    public function rows()
    {
        return $this->hasMany(TradingAccountRow::class, 'trading_account_id');
    }

    public function debitRows()
    {
        return $this->hasMany(TradingAccountRow::class, 'trading_account_id')
            ->where('side', 'dr')
            ->orderBy('sort_order');
    }

    public function creditRows()
    {
        return $this->hasMany(TradingAccountRow::class, 'trading_account_id')
            ->where('side', 'cr')
            ->orderBy('sort_order');
    }
}