<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinalInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'final_invoice_no',
        'proforma_invoice_id',
        'client_id',
        'invoice_date',
        'subtotal',
        'tax',
        'discount',
        'total',
        'created_by',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function proformaInvoice()
    {
        return $this->belongsTo(ProformaInvoice::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function items()
    {
        return $this->hasMany(FinalInvoiceItem::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
