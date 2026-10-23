<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    // Table name (optional if it follows Laravel convention)
    protected $table = 'coupons';

    // Mass assignable fields
    protected $fillable = [
        'code',
        'type',
        'value',
        'min_amount',
        'max_discount',
        'start_date',
        'end_date',
        'max_usage',
        'max_usage_per_client',
        'status',
        'created_by',
    ];

    // Relationships

    // Coupon created by admin/marketing staff
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Coupon can apply to many services (through pivot table)
    public function services()
    {
        return $this->belongsToMany(Service::class, 'coupon_service', 'coupon_id', 'service_id');
    }

    // Coupon usage by clients
    public function usages()
    {
        return $this->hasMany(CouponUsage::class, 'coupon_id');
    }
}
