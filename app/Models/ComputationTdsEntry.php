<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComputationTdsEntry extends Model
{
    protected $table = 'computation_tds_entries';
    
    protected $fillable = [
        'computation_id', 'tan_number', 'deductor_name', 'section_code',
        'amount_paid', 'tds_deducted', 'tds_claimed', 'sort_order'
    ];
    
    protected $casts = [
        'amount_paid' => 'decimal:2',
        'tds_deducted' => 'decimal:2',
        'tds_claimed' => 'decimal:2'
    ];
    
    public function computation()
    {
        return $this->belongsTo(Computation::class);
    }
}