<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComputationOtherSourceEntry extends Model
{
    use HasFactory;

    protected $table = 'computation_other_source_entries';

    protected $fillable = [
        'computation_id',
        'particular',
        'section',
        'amount',
        'remarks',
        'sort_order',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'sort_order' => 'integer',
    ];

    /**
     * Parent Computation
     */
    public function computation()
    {
        return $this->belongsTo(
            Computation::class,
            'computation_id'
        );
    }
}