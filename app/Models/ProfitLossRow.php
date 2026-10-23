<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProfitLossRow extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'particular_name',
        'amount',
        'sort_order',
        'is_system',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_system' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(
            ProfitLossCategory::class,
            'category_id'
        );
    }
}