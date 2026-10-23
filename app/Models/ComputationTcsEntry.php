<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComputationTcsEntry extends Model
{
    use HasFactory;

    protected $table = 'computation_tcs_entries';

    protected $fillable = [
        'computation_id',

        'tan_number',
        'collector_name',
        'section_code',

        'certificate_no',
        'financial_year',

        'amount_collected',
        'tcs_collected',
        'tcs_claimed',

        'remarks',

        'sort_order',
    ];

    protected $casts = [
        'amount_collected' => 'decimal:2',
        'tcs_collected'    => 'decimal:2',
        'tcs_claimed'      => 'decimal:2',
        'sort_order'       => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function computation()
    {
        return $this->belongsTo(Computation::class);
    }
}