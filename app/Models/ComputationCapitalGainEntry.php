<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComputationCapitalGainEntry extends Model
{
    use HasFactory;

    protected $table = 'computation_capital_gain_entries';

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
        return $this->belongsTo(Computation::class, 'computation_id');
    }

    /**
     * Scope: Short Term Capital Gain
     */
    public function scopeStcg($query)
    {
        return $query->where('section', 'STCG');
    }

    /**
     * Scope: Long Term Capital Gain
     */
    public function scopeLtcg($query)
    {
        return $query->where('section', 'LTCG');
    }

    /**
     * Scope: Exemptions
     */
    public function scopeExemptions($query)
    {
        return $query->where('section', 'EXEMPTION');
    }
}