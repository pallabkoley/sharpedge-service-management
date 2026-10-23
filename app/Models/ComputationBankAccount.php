<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComputationBankAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'computation_id',
        'bank_name',
        'account_number',
        'ifsc',
        'is_refund_account',
        'sort_order',
    ];

    protected $casts = [
        'is_refund_account' => 'boolean',
    ];

    public function computation()
    {
        return $this->belongsTo(Computation::class);
    }
}