<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DebitCreditNoteRegister extends Model
{
    use HasFactory;

    protected $fillable = [
        'computation_id',

        'note_type',
        'gst_type',
        'calculation_mode',
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

        'created_by',
        'updated_by',
        'approved_by',
        'approved_at',
        'finalized_at',
    ];

    protected $casts = [
        'signing_date' => 'date',
        'approved_at' => 'datetime',
        'finalized_at' => 'datetime',
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
        return $this->hasMany(DebitCreditNoteRegisterRow::class)
            ->orderBy('row_order');
    }
}