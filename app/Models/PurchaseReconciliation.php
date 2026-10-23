<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseReconciliation extends Model
{
    use HasFactory;

    protected $fillable = [

        'computation_id',

        'reconciliation_type',
        'status',

        'prepared_by',
        'chartered_accountant',
        'membership_number',
        'place',
        'signing_date',

        'itr_acknowledgement_no',
        'audit_acknowledgement_no',
        'ie10_acknowledgement_no',

        'udin',

        'ca_signature',
        'assessee_signature',
    ];

    protected $casts = [

        'signing_date' => 'date',
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

    public function rows()
    {
        return $this->hasMany(PurchaseReconciliationRow::class)
            ->orderBy('row_order');
    }
}