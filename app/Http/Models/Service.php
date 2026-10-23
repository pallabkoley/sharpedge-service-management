<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// Related Models
use App\Models\ServiceCategory;
use App\Models\DocumentRequirement;
use App\Models\WorkProcess;
use App\Models\OrderItem;
use App\Models\Client;
use App\Models\OrderDocument;

class Service extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * =========================
     * TABLE
     * =========================
     */
    protected $table = 'services';

    /**
     * =========================
     * MASS ASSIGNMENT
     * =========================
     */
    protected $fillable = [
        'service_code',
        'service_name',
        'service_category_id',

        // Fees
        'govt_fee',
        'our_fee',

        // Tax & visibility
        'is_taxable',
        'tax_percentage',
        'allow_discount',
        'visible_to_client',

        // Meta
        'processing_time',
        'description',
        'status',
        'order',
    ];

    /**
     * =========================
     * CASTS
     * =========================
     */
protected $casts = [
    'our_fee'        => 'float',
    'govt_fee'       => 'float',
    'platform_fee'   => 'float',
    'tax_percentage' => 'float',

    'is_taxable'        => 'boolean',
    'allow_discount'    => 'boolean',
    'visible_to_client' => 'boolean',

    'order' => 'integer',
];

    /**
     * =========================
     * RELATIONSHIPS
     * =========================
     */

    // Belongs to Service Category
    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }

    // Document Requirements
    public function documentRequirements()
    {
        return $this->hasMany(DocumentRequirement::class, 'service_id')
                    ->orderBy('order');
    }

    // Active Work Process
    public function workProcess()
    {
        return $this->hasOne(WorkProcess::class, 'service_id')
                    ->where('status', 'active');
    }

    // Order Items
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'service_id');
    }

    // Order Documents
    public function orderDocuments()
    {
        return $this->hasMany(OrderDocument::class, 'service_id');
    }

    // Clients using this service
    public function clients()
    {
        return $this->belongsToMany(Client::class, 'client_services')
            ->withPivot([
                'status',
                'assigned_date',
                'start_date',
                'end_date',
                'created_by'
            ])
            ->withTimestamps();
    }

    /**
     * =========================
     * SCOPES
     * =========================
     */
public function workflowHealths()
{
    return $this->hasMany(ServiceWorkflowHealth::class, 'service_id');
}
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopeVisibleToClient($query)
    {
        return $query->where('visible_to_client', true);
    }

    public function scopeHasDocumentRequirements($query)
    {
        return $query->whereHas('documentRequirements', function ($q) {
            $q->where('status', 'active');
        });
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('service_name', 'like', "%{$search}%")
              ->orWhere('service_code', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    /**
     * =========================
     * ACCESSORS & HELPERS
     * =========================
     */

    // Alias
    public function getNameAttribute(): string
    {
        return $this->service_name;
    }

    /**
     * Govt fee numeric value
     */
    public function getGovtFeeAmountAttribute(): float
    {
        return is_numeric($this->govt_fee)
            ? (float) $this->govt_fee
            : 0.0;
    }

    /**
     * Govt fee formatted
     */
    public function getFormattedGovtFeeAttribute(): string
    {
        if ($this->govt_fee === null || $this->govt_fee === '') {
            return 'N/A';
        }

        return is_numeric($this->govt_fee)
            ? '₹' . number_format((float) $this->govt_fee, 2)
            : $this->govt_fee;
    }

    /**
     * Our fee formatted
     */
    public function getFormattedOurFeeAttribute(): string
    {
        return $this->our_fee !== null
            ? '₹' . number_format($this->our_fee, 2)
            : 'N/A';
    }

    /**
     * Tax amount on our fee
     */
    public function getTaxAmountAttribute(): float
    {
        if (!$this->is_taxable || !$this->tax_percentage || !$this->our_fee) {
            return 0.0;
        }

        return ($this->our_fee * $this->tax_percentage) / 100;
    }

    /**
     * Total payable (our fee + tax)
     */
    public function getTotalAmountAttribute(): float
    {
        return ($this->our_fee ?? 0) + $this->tax_amount;
    }
}
