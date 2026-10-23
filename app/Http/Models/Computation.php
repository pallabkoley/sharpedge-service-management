<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Computation extends Model
{
    use HasFactory;

    protected $fillable = [

        'client_id',
        'created_by',
        'client_type_id',

        'assessment_year',
        'financial_year',
        'previous_year',

        'return_type',
        'tax_regime',

        'filing_status',
        'computation_status',
        'payment_status',

        'computation_date',

        'verified_by',
        'auditor_name',
        'udin',
        'din_number',

        'bank_name',
        'refund_bank_ifsc',

        'notes',
        'status',
    ];

    protected $casts = [

        'computation_date' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function clientType()
    {
        return $this->belongsTo(ClientType::class);
    }

    public function values()
    {
        return $this->hasMany(ComputationValue::class);
    }

    public function tdsEntries()
    {
        return $this->hasMany(ComputationTdsEntry::class);
    }

    public function advanceTaxEntries()
    {
        return $this->hasMany(ComputationAdvanceTaxEntry::class);
    }
}