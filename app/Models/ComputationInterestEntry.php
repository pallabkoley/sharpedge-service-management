<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComputationInterestEntry extends Model
{
    use HasFactory;

    protected $table = 'computation_interest_entries';

    protected $fillable = [
        'computation_id',

        'interest_234a',
        'interest_234b',
        'interest_234c',
        'fee_234f',

        'remarks_234a',
        'remarks_234b',
        'remarks_234c',
        'remarks_234f',
    ];

    protected $casts = [
        'interest_234a' => 'decimal:2',
        'interest_234b' => 'decimal:2',
        'interest_234c' => 'decimal:2',
        'fee_234f'      => 'decimal:2',
    ];

    public function computation()
    {
        return $this->belongsTo(Computation::class);
    }
}