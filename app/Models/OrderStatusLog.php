<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderStatusLog extends Model
{
    use HasFactory;

    protected $table = 'order_status_log';

    protected $fillable = [
        'order_id',
        'status',
        'changed_by',
        'remarks',
    ];

    /* =========================
       Relationships
    ========================== */

    // Belongs to Order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Staff who changed the status
    public function changedBy()
    {
        return $this->belongsTo(Staff::class, 'changed_by');
    }
//     public function statusLogs()
// {
//     return $this->hasMany(OrderStatusLog::class);
// }

}
