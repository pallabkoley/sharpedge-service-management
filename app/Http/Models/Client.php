<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Order;
use App\Models\CouponUsage;
use App\Models\Meeting;
use App\Models\Payment;
use App\Models\Document;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_code',
        'user_id',
        'created_by',

        'name',
        'email',
        'phone',
        'profile_photo',
        'status',

        'is_credit_allowed',

        'dob',
        'gender',

        'address',
        'city',
        'state',
        'pincode',

        'company_name',
        'gst_number',

        'preferred_contact',
        'source',
        'notes',
    ];

    protected $casts = [
        'dob' => 'date',
        'is_credit_allowed' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Auto Client Code
    |--------------------------------------------------------------------------
    */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($client) {
            if (empty($client->client_code)) {
                $nextId = self::max('id') ?? 0;
                $client->client_code = 'CLT-' . str_pad($nextId + 1, 6, '0', STR_PAD_LEFT);
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function meetings()
    {
        return $this->hasMany(Meeting::class, 'client_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'client_id');
    }

    public function couponUsages()
    {
        return $this->hasMany(CouponUsage::class, 'client_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'client_id');
    }
public function computations()
{
    return $this->hasMany(Computation::class);
}
    // 🔥 Client Documents
    public function documents()
    {
        return $this->hasMany(Document::class, 'client_id');
    }
}