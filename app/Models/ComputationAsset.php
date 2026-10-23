<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ComputationAsset extends Model
{
    protected $fillable = [
        'computation_id',
        'asset_name',
        'block',
        'unit',
        'opening_wdv',
        'purchase_amount',
        'sale_amount',
        'purchase_date',
        'sale_date',
        'depreciable_wdv',
        'business_use_percentage',
        'depreciation_amount',
        'deduction_amount',
        'closing_wdv',
        'remarks',
        'revaluation_appreciation', // ADDED: Missing column
        'depreciation_rate',        // ADDED: Missing column
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'sale_date' => 'date',
        'opening_wdv' => 'float',
        'purchase_amount' => 'float',
        'sale_amount' => 'float',
        'revaluation_appreciation' => 'float',
        'business_use_percentage' => 'float',
        'depreciation_rate' => 'float',
    ];

    public function computation()
    {
        return $this->belongsTo(Computation::class);
    }

    public function breakdowns()
    {
        return $this->hasMany(ComputationAssetBreakdown::class, 'computation_asset_id');
    }

    // ==========================================
    // CENTRAL CALCULATION ENGINE (Backend)
    // ==========================================

    public function calculateDepreciableWDV(): float
    {
        return ($this->opening_wdv ?? 0) 
            + ($this->purchase_amount ?? 0) 
            + ($this->revaluation_appreciation ?? 0) 
            - ($this->sale_amount ?? 0);
    }

    public function calculateDepreciationAmount(): float
    {
        $base = $this->calculateDepreciableWDV();
        $rate = $this->depreciation_rate ?? 0;

        if ($rate <= 0 || $base <= 0) {
            return 0;
        }

        $halfYear = false;
        $fyStart = Carbon::createFromDate(date('Y'), 4, 1);
        $fyEnd = Carbon::createFromDate(date('Y') + 1, 3, 31);

        // Half-year logic: If purchase_date is after FY start
        if ($this->purchase_date && $this->purchase_date->greaterThan($fyStart)) {
            $halfYear = true;
        }

        // Half-year logic: If sale_date exists and is before FY end
        if ($this->sale_date && $this->sale_date->lessThan($fyEnd)) {
            $halfYear = true;
        }

        $depreciation = $base * ($rate / 100);
        if ($halfYear) {
            $depreciation = $depreciation * 0.5;
        }

        return max(0, $depreciation);
    }

    public function calculateDeductionAmount(): float
    {
        $dep = $this->calculateDepreciationAmount();
        $busUse = ($this->business_use_percentage ?? 100) / 100;
        return $dep * $busUse;
    }

    public function calculateClosingWDV(): float
    {
        return $this->calculateDepreciableWDV() - $this->calculateDepreciationAmount();
    }
}