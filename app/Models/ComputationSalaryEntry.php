<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComputationSalaryEntry extends Model
{
    use HasFactory;

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
    ];

    /**
     * Get the computation that owns the salary entry.
     */
    public function computation()
    {
        return $this->belongsTo(Computation::class);
    }
}