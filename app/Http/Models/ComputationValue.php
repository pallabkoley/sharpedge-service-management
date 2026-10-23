<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComputationValue extends Model
{
    protected $fillable = [

        'computation_id',
        'head_id',

        'column_key',

        'value',
        'numeric_value',
        'formatted_value',

        'is_overridden',

        'remarks',
    ];

    protected $casts = [

        'numeric_value' => 'decimal:2',
        'is_overridden' => 'boolean',
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

    public function head()
    {
        return $this->belongsTo(ComputationHead::class);
    }
}