<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseReconciliationRow extends Model
{
    use HasFactory;

    protected $fillable = [

        'purchase_reconciliation_id',

        'row_order',
        'row_type',
        'row_label',

        'taxable_value',
        'exempted_value',

        'igst',
        'cgst',
        'sgst',

        'cgst_tds',
        'sgst_tds',

        'invoice_value',

        'remarks',

        'is_system_row',
    ];

    protected $casts = [

        'taxable_value' => 'decimal:2',
        'exempted_value' => 'decimal:2',

        'igst' => 'decimal:2',
        'cgst' => 'decimal:2',
        'sgst' => 'decimal:2',

        'cgst_tds' => 'decimal:2',
        'sgst_tds' => 'decimal:2',

        'invoice_value' => 'decimal:2',

        'is_system_row' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function reconciliation()
    {
        return $this->belongsTo(PurchaseReconciliation::class, 'purchase_reconciliation_id');
    }
}