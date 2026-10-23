<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormulaLog extends Model
{
    protected $fillable = [
        'computation_id',
        'head_id',
        'formula',
        'result'
    ];

    public function computation()
    {
        return $this->belongsTo(Computation::class);
    }
}