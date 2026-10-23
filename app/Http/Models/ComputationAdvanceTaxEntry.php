<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComputationAdvanceTaxEntry extends Model
{
    protected $fillable = [

        'computation_id',

        'payment_date',

        'bsr_code',
        'challan_no',

        'installment',

        'amount_paid',

        'remarks',

        'sort_order',
    ];

    protected $casts = [

        'payment_date' => 'date',
        'amount_paid' => 'decimal:2',
    ];

    public function computation()
    {
        return $this->belongsTo(Computation::class);
    }
}