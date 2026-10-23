<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DebitCreditNoteBreakup extends Model
{
    use HasFactory;

    protected $fillable = [
        'debit_credit_note_register_row_id',

        'invoice_no',
        'invoice_date',

        'party_name',
        'gstin',

        'taxable_value',
        'gst_rate',

        'igst',
        'cgst',
        'sgst',

        'invoice_value',

        'remarks',
    ];

    protected $casts = [
        'invoice_date' => 'date',

        'taxable_value' => 'decimal:2',
        'gst_rate' => 'decimal:2',

        'igst' => 'decimal:2',
        'cgst' => 'decimal:2',
        'sgst' => 'decimal:2',

        'invoice_value' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function row()
    {
        return $this->belongsTo(
            DebitCreditNoteRegisterRow::class,
            'debit_credit_note_register_row_id'
        );
    }
}