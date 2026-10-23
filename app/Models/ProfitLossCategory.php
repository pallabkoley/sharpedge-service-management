<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProfitLossCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'profit_loss_account_id',
        'side',
        'category_name',
        'sort_order',
        'is_system',
    ];

    protected $casts = [
        'is_system' => 'boolean',
    ];

    public function account()
    {
        return $this->belongsTo(
            ProfitLossAccount::class,
            'profit_loss_account_id'
        );
    }

    public function rows()
    {
        return $this->hasMany(
            ProfitLossRow::class,
            'category_id'
        )->orderBy('sort_order');
    }
}