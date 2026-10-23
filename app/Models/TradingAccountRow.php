<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TradingAccountRow extends Model
{
    protected $fillable = [
        'trading_account_id',
        'side',
        'row_type',
        'particular_name',
        'amount',
        'sort_order',
    ];

    public function tradingAccount()
    {
        return $this->belongsTo(
            TradingAccount::class,
            'trading_account_id'
        );
    }
}