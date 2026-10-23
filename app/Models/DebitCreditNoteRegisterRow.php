<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DebitCreditNoteRegisterRow extends Model
{
    use HasFactory;

    protected $fillable = [
        'debit_credit_note_register_id',

        'row_order',
        'row_type',
        'row_label',

        'taxable_value',
        'gst_rate',

        'igst',
        'cgst',
        'sgst',

        'invoice_value',

        'remarks',

        'has_breakup',
        'breakup_total',

        'is_locked',
        'is_system_row',
    ];

    protected $casts = [
        'taxable_value' => 'decimal:2',
        'gst_rate' => 'decimal:2',

        'igst' => 'decimal:2',
        'cgst' => 'decimal:2',
        'sgst' => 'decimal:2',

        'invoice_value' => 'decimal:2',
        'breakup_total' => 'decimal:2',

        'has_breakup' => 'boolean',
        'is_locked' => 'boolean',
        'is_system_row' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function register()
    {
        return $this->belongsTo(
            DebitCreditNoteRegister::class,
            'debit_credit_note_register_id'
        );
    }

    public function breakups()
    {
        return $this->hasMany(
            DebitCreditNoteBreakup::class,
            'debit_credit_note_register_row_id'
        );
    }
}