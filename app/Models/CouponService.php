<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CouponService extends Model
{
    use HasFactory;

    protected $table = 'coupon_service';

    protected $fillable = [
        'coupon_id',
        'service_id',
    ];
}
