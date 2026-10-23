<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComputationAssetBreakdown extends Model
{
    protected $fillable = [
        'computation_asset_id',
        'amount',
        'depreciation_rate',
        'depreciation_amount',
    ];

    public function asset()
    {
        return $this->belongsTo(
            ComputationAsset::class,
            'computation_asset_id'
        );
    }
}