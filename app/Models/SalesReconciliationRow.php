<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesReconciliationRow extends Model
{
    use HasFactory;

    protected $fillable = [

        'sales_reconciliation_id',

        'row_order',
        'row_type',
        'row_label',

        'taxable_value',
        'igst',
        'cgst',
        'sgst',
        'sgst_tds',
        'cgst_tds',
        'invoice_value',

        'remarks',

        'is_system_row',
    ];

    protected $casts = [

        'taxable_value' => 'decimal:2',
        'igst' => 'decimal:2',
        'cgst' => 'decimal:2',
        'sgst' => 'decimal:2',
        'sgst_tds' => 'decimal:2',
        'cgst_tds' => 'decimal:2',
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
        return $this->belongsTo(
            SalesReconciliation::class,
            'sales_reconciliation_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isCustom()
    {
        return $this->row_type === 'custom';
    }

    public function isMonth()
    {
        return $this->row_type === 'month';
    }
}