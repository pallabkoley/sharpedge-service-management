<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    /* =========================
       MASS ASSIGNMENT
    ========================== */

    protected $fillable = [
        'order_id',
        'client_id',
        'service_id',

        'marketing_staff_id',
        'created_by_admin',

        // pricing
        'base_price',
        'estimated_govt_fee',
        'final_govt_fee',
        'discount_percent',
        'discount_amount',
        'final_amount',

        // lifecycle
        'order_status',
        'verified_by',
        'verified_at',

        // invoice
        'invoice_generated',
        'invoice_number',
        'invoice_date',
        'invoice_path',

        // certificate
        'certificate_path',

        // coupon
        'coupon_id',
    ];

    /* =========================
       CASTS
    ========================== */

    protected $casts = [
        'base_price'         => 'decimal:2',
        'estimated_govt_fee' => 'decimal:2',
        'final_govt_fee'     => 'decimal:2',
        'discount_percent'   => 'decimal:2',
        'discount_amount'    => 'decimal:2',
        'final_amount'       => 'decimal:2',

        'invoice_generated'  => 'boolean',
        'verified_at'        => 'datetime',
        'invoice_date'       => 'date',
    ];

    /* =========================
       RELATIONSHIPS
    ========================== */

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id')->withTrashed();
    }

    public function marketingStaff()
    {
        return $this->belongsTo(Staff::class, 'marketing_staff_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function latestPayment()
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'order_id');
    }

    public function supportTickets()
    {
        return $this->hasMany(SupportTicket::class, 'order_id');
    }

    public function workflowHealth()
    {
        return $this->hasOne(ServiceWorkflowHealth::class, 'order_id');
    }

    public function documents()
    {
        return $this->hasMany(OrderDocument::class, 'order_id');
    }

    public function statusLogs()
    {
        return $this->hasMany(OrderStatusLog::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function couponUsages()
    {
        return $this->hasMany(CouponUsage::class, 'order_id');
    }

    /* =========================
       🔥 LEDGER CALCULATIONS
    ========================== */

    // ✅ Total Paid (only approved)
    public function getPaidAmountAttribute()
    {
        if ($this->relationLoaded('payments')) {
            return $this->payments
                ->where('status', 'approved')
                ->sum('amount');
        }

        return $this->payments()
            ->where('status', 'approved')
            ->sum('amount');
    }

    // ✅ Due Amount
    public function getDueAmountAttribute()
    {
        return max(0, $this->final_amount - $this->paid_amount);
    }

    // ✅ Payment Status
    public function getPaymentStatusAttribute()
    {
        $paid  = $this->paid_amount;
        $total = $this->final_amount;

        if ($total <= 0) return 'Draft';
        if ($paid == 0) return 'Pending';
        if ($paid < $total) return 'Partially Paid';

        return 'Paid';
    }

    // ✅ Progress %
    public function getProgressPercentageAttribute()
    {
        if ($this->final_amount <= 0) return 0;

        return round(($this->paid_amount / $this->final_amount) * 100, 2);
    }

    /* =========================
       PAYMENT STATE HELPERS
    ========================== */

    public function isPaid(): bool
    {
        return $this->paid_amount >= $this->final_amount;
    }

    public function isPartialPayment(): bool
    {
        return $this->paid_amount > 0 && $this->paid_amount < $this->final_amount;
    }

    public function isUnpaid(): bool
    {
        return $this->paid_amount == 0;
    }

    /* =========================
       DISPLAY HELPERS
    ========================== */

    public function getDisplayGovtFeeAttribute()
    {
        return $this->invoice_generated
            ? $this->final_govt_fee
            : $this->estimated_govt_fee;
    }

    public function getMarketingStatusAttribute()
    {
        return $this->payment_status;
    }

    /* =========================
       SUPPORT TICKET
    ========================== */

    public function hasOpenSupportTicket(): bool
    {
        return $this->supportTickets()
            ->whereIn('status', ['open','in_progress','pending','reopened'])
            ->exists();
    }

    public function canBeCompleted(): bool
    {
        return !$this->hasOpenSupportTicket();
    }

    /* =========================
       INVOICE HELPERS
    ========================== */

    public function isInvoiceLocked(): bool
    {
        return $this->invoice_generated && $this->isPaid();
    }

    public function canGenerateInvoice(): bool
    {
        return $this->isPaid() && !$this->invoice_generated;
    }

    /* =========================
       CERTIFICATE
    ========================== */

    public function hasCertificate(): bool
    {
        return !empty($this->certificate_path);
    }

    public function getCertificateUrlAttribute(): ?string
    {
        if (!$this->certificate_path) return null;

        return asset('storage/' . $this->certificate_path);
    }

    public function canUploadCertificate(): bool
    {
        if (!$this->workflowHealth) return false;

        return (
            $this->workflowHealth->health_status === 'READY' &&
            $this->workflowHealth->order_ok === true &&
            !$this->certificate_path
        );
    }

    /* =========================
       WORK STATUS
    ========================== */

    public function getCalculatedWorkStatusAttribute()
    {
        if (!$this->tasks || $this->tasks->count() === 0) {
            return 'unassigned';
        }

        $total = $this->tasks->count();
        $completed = $this->tasks->whereIn('status', ['completed','verified'])->count();

        if ($completed === $total) return 'completed';
        if ($completed > 0) return 'in_progress';

        return 'assigned';
    }

    /* =========================
       STATIC HELPERS
    ========================== */

    public static function paymentApprovalCount(): int
    {
        return Payment::where('status','submitted')->count();
    }
    public function supportDocuments()
{
    return $this->hasMany(OrderDocument::class, 'order_id');
}
    
    protected static function boot()
{
    parent::boot();

    static::deleting(function ($order) {

        $order->payments()->delete();
        $order->items()->delete();
        $order->statusLogs()->delete();
        $order->documents()->delete();
        $order->couponUsages()->delete();

        // optional (if used)
        $order->tasks()->delete();
        $order->supportTickets()->delete();
        $order->workflowHealth()->delete();
    });
}
}