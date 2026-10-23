<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComputationInvestment extends Model
{
    protected $fillable = [
        'computation_id',
        'investment_name',
        'amount',
        'remarks',
    ];

    public function computation()
    {
        return $this->belongsTo(Computation::class);
    }
}