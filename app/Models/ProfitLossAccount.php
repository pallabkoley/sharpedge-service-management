<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProfitLossAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'computation_id',
    ];

    public function computation()
    {
        return $this->belongsTo(
            Computation::class,
            'computation_id'
        );
    }

    public function categories()
    {
        return $this->hasMany(
            ProfitLossCategory::class,
            'profit_loss_account_id'
        )->orderBy('sort_order');
    }
}