<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComputationAdvanceTaxEntry extends Model
{
    protected $table = 'computation_advance_tax_entries';
    
    protected $fillable = [
        'computation_id', 'payment_date', 'bsr_code', 'challan_no',
        'installment', 'amount_paid', 'remarks', 'sort_order'
    ];
    
    protected $casts = [
        'amount_paid' => 'decimal:2',
        'payment_date' => 'date'
    ];
    
    public function computation()
    {
        return $this->belongsTo(Computation::class);
    }
}