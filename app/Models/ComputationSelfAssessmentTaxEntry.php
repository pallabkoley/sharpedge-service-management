<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComputationSelfAssessmentTaxEntry extends Model
{
    use HasFactory;

    protected $table = 'computation_self_assessment_tax_entries';

    protected $fillable = [
        'computation_id',
        'payment_date',
        'bsr_code',
        'challan_no',
        'amount_paid',
        'remarks',
        'sort_order',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount_paid'  => 'decimal:2',
    ];

    public function computation()
    {
        return $this->belongsTo(Computation::class);
    }
}