<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProformaInvoice extends Model
{
    use HasFactory;

    protected $table = 'proforma_invoices';

    protected $fillable = [
        'proforma_no',
        'client_id',
        'staff_id',
        'subtotal',
        'tax',
        'discount',
        'total',
        'status',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // ProformaInvoice belongs to a Client
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    // ProformaInvoice belongs to a Staff
    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    // ProformaInvoice has many items (services)
    public function items()
    {
        return $this->hasMany(ProformaInvoiceItem::class, 'proforma_invoice_id');
    }

    // ProformaInvoice has many payments
    public function payments()
    {
        return $this->hasMany(Payment::class, 'proforma_invoice_id');
    }

    // ProformaInvoice has one final invoice
    public function finalInvoice()
    {
        return $this->hasOne(FinalInvoice::class, 'proforma_invoice_id');
    }
}
