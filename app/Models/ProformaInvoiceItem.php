<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProformaInvoiceItem extends Model
{
    use HasFactory;

    protected $table = 'proforma_invoice_items';

    protected $fillable = [
        'proforma_invoice_id',
        'service_id',
        'price',
        'qty',
        'total',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // Item belongs to a proforma invoice
    public function proformaInvoice()
    {
        return $this->belongsTo(ProformaInvoice::class, 'proforma_invoice_id');
    }

    // Item belongs to a service
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}
