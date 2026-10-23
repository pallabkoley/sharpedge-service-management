<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesReconciliation extends Model
{
    use HasFactory;

    protected $fillable = [
        'computation_id',
        'reconciliation_type',

        'prepared_by',
        'chartered_accountant',
        'membership_number',
        'place',
        'signing_date',

        'itr_acknowledgement_no',
        'audit_acknowledgement_no',
        'ie10_acknowledgement_no',
        'udin',

        'assessee_signature',
        'ca_signature',

        'status',
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
        return $this->hasMany(
            SalesReconciliationRow::class,
            'sales_reconciliation_id'
        )->orderBy('row_order');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function registerRows()
    {
        return $this->rows()->where('row_type', 'month');
    }

    public function customRows()
    {
        return $this->rows()->where('row_type', 'custom');
    }
}