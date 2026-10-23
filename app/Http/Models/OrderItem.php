<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $table = 'order_items';

    /* =========================
       MASS ASSIGNMENT
    ========================== */

    protected $fillable = [
        'order_id',
        'service_id',
        'price',
        'quantity',
        'total',
        'item_type',       
        'invoice_status', 
        'is_addon',
        'parent_item_id',
        'added_by',
        'added_at',
        'remarks',
    ];

    /* =========================
       CASTS
    ========================== */

    protected $casts = [
        'price'           => 'decimal:2',
        'total'           => 'decimal:2',
        'quantity'        => 'integer',
    ];

    /* =========================
       RELATIONSHIPS
    ========================== */

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /* =========================
       SCOPES (IMPORTANT FOR ADD-ON)
    ========================== */

    public function scopeBase($query)
    {
        return $query->where('item_type', 'BASE');
    }

    public function scopeAddOn($query)
    {
        return $query->where('item_type', 'ADDON');
    }

    public function scopePendingInvoice($query)
    {
        return $query->where('invoice_status', 'PENDING_INVOICE');
    }

    public function scopeInvoiced($query)
    {
        return $query->where('invoice_status', 'INVOICED');
    }
}
