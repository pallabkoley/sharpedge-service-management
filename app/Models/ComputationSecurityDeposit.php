<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComputationSecurityDeposit extends Model
{
    protected $fillable = [
        'computation_id',
        'deposit_name',
        'amount',
        'remarks',
    ];

    public function computation()
    {
        return $this->belongsTo(Computation::class);
    }
}