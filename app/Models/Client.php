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

        // =====================================================
        // BASIC
        // =====================================================
        'client_code',
        'user_id',
        'created_by',

        // =====================================================
        // PERSONAL INFORMATION
        // =====================================================
        'name',
        'father_name',
        'email',
        'phone',
        'alternate_mobile',
        'profile_photo',

        // =====================================================
        // IDENTITY
        // =====================================================
        'dob',
        'pan',
        'aadhaar',
        'gender',
        'client_type',

        // =====================================================
        // ADDRESS
        // =====================================================
        'address',
        'city',
        'state',
        'pincode',

        // =====================================================
        // BUSINESS
        // =====================================================
        'company_name',
        'gst_number',
        'tan',
        'cin_llpin',
        'nature_of_business',
        'constitution',
        'approx_annual_turnover',

        // =====================================================
        // TAX
        // =====================================================
        'residential_status',
        'last_udin_used',

        // =====================================================
        // OTHER
        // =====================================================
        'preferred_contact',
        'source',
        'notes',
        'compliance_notes',

        // =====================================================
        // STATUS
        // =====================================================
        'status',
        'is_credit_allowed',
    ];

    protected $casts = [

        // Identity
        'dob' => 'date',

        // Business
        'approx_annual_turnover' => 'decimal:2',

        // Boolean
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

                $client->client_code =
                    'CLT-' . str_pad(
                        $nextId + 1,
                        6,
                        '0',
                        STR_PAD_LEFT
                    );
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Client User Account
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * User who created the client
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Computations
     */
    public function computations()
    {
        return $this->hasMany(Computation::class);
    }

    /**
     * Meetings
     */
    public function meetings()
    {
        return $this->hasMany(Meeting::class, 'client_id');
    }

    /**
     * Orders
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'client_id');
    }

    /**
     * Coupon Usages
     */
    public function couponUsages()
    {
        return $this->hasMany(CouponUsage::class, 'client_id');
    }

    /**
     * Payments
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'client_id');
    }

    /**
     * Documents
     */
    public function documents()
    {
        return $this->hasMany(Document::class, 'client_id');
    }

    /*
    |--------------------------------------------------------------------------
    | CLIENT MASTER RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    /**
     * Bank Accounts
     *
     * One client can have multiple bank accounts.
     */
    public function bankAccounts()
    {
        return $this->hasMany(ClientBankAccount::class, 'client_id');
    }

    /**
     * GST & IT Cases
     */
    public function cases()
    {
        return $this->hasMany(ClientCase::class, 'client_id');
    }

    /**
     * Compliance Items
     */
    public function compliances()
    {
        return $this->hasMany(ClientCompliance::class, 'client_id');
    }

    /**
     * Monthly Turnover
     */
    public function turnovers()
    {
        return $this->hasMany(ClientTurnover::class, 'client_id');
    }

    /**
     * Portal Credentials
     */
    public function portalCredentials()
    {
        return $this->hasMany(
            ClientPortalCredential::class,
            'client_id'
        );
    }

    /**
     * Custom Field Values
     */
    public function customFieldValues()
    {
        return $this->hasMany(
            ClientCustomFieldValue::class,
            'client_id'
        );
    }
}