<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

class Computation extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Table & Primary Key
    |--------------------------------------------------------------------------
    */
    protected $table = 'computations';
    protected $primaryKey = 'id';
    public $timestamps = true;

    /*
    |--------------------------------------------------------------------------
    | Constants – Tax Regime
    |--------------------------------------------------------------------------
    */
    public const REGIME_OLD = 'old';
    public const REGIME_NEW = 'new';
    public static array $regimes = [
        self::REGIME_OLD => 'Old Regime',
        self::REGIME_NEW => 'New Regime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Constants – Filing Status
    |--------------------------------------------------------------------------
    */
    public const FILING_ORIGINAL = 'original';
    public const FILING_REVISED  = 'revised';
    public const FILING_BELATED  = 'belated';
    public static array $filingStatuses = [
        self::FILING_ORIGINAL => 'Original',
        self::FILING_REVISED  => 'Revised',
        self::FILING_BELATED  => 'Belated',
    ];

    /*
    |--------------------------------------------------------------------------
    | Constants – Computation Status
    |--------------------------------------------------------------------------
    */
    public const STATUS_DRAFT     = 'draft';
    public const STATUS_FINAL     = 'final';
    public const STATUS_FILED     = 'filed';
    public const STATUS_SUBMITTED = 'submitted';
    public static array $computationStatuses = [
        self::STATUS_DRAFT     => 'Draft',
        self::STATUS_FINAL     => 'Final',
        self::STATUS_FILED     => 'Filed',
        self::STATUS_SUBMITTED => 'Submitted',
    ];

    /*
    |--------------------------------------------------------------------------
    | Constants – Payment Status
    |--------------------------------------------------------------------------
    */
    public const PAYMENT_PAID        = 'paid';
    public const PAYMENT_UNPAID      = 'unpaid';
    public const PAYMENT_REFUND_DUE  = 'refund_due';
    public static array $paymentStatuses = [
        self::PAYMENT_PAID       => 'Paid',
        self::PAYMENT_UNPAID     => 'Unpaid',
        self::PAYMENT_REFUND_DUE => 'Refund Due',
    ];

    /*
    |--------------------------------------------------------------------------
    | Fillable
    |--------------------------------------------------------------------------
    */
    protected $fillable = [
        'client_id',
        'created_by',
        'client_type_id',

        'trade_name',
        'trade_code',
        'income_tax_id',
        'income_tax_password',
        'gmail_password',

        'assessment_year',
        'financial_year',
        'previous_year',

        'return_type',
        'tax_regime',
        'constitution',

        'residential_section',
        'residential_year',

        'filing_status',
        'computation_status',

        'payment_status',
        'payment_mode',
        'payment_date',
        'paid_amount',
        'bsr_no',

        'computation_date',

        'verified_by',
        'verify_status',
        'verify_success',

        'auditor_name',
        'membership_number',
        'audit_place',
        'audit_date',
        'liable_under_44ab',

        'udin',
        'din_number',

        'itr_acknowledgement_number',
        'audit_acknowledgement_number',
        'ie10_acknowledgement_number',

        'form_under_section',
        'form_no',

        'alternate_mobile',
        'barcode_path',

        'notes',
        'status',

        'submitted_at',
        'submitted_by',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */
    protected $casts = [
        'computation_date'  => 'date:Y-m-d',
        'payment_date'      => 'date:Y-m-d',
        'audit_date'        => 'date:Y-m-d',
        'submitted_at'      => 'datetime',

        'paid_amount'       => 'decimal:2',
        'liable_under_44ab' => 'boolean',
        'status'            => 'boolean',

        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Hidden (for API responses)
    |--------------------------------------------------------------------------
    */
    protected $hidden = [
        'gmail_password',
        'income_tax_password',
    ];

    /*
    |--------------------------------------------------------------------------
    | Appends (computed attributes)
    |--------------------------------------------------------------------------
    */
    protected $appends = [
        'tax_regime_label',
        'computation_status_label',
        'filing_status_label',
        'payment_status_label',
        'formatted_paid_amount',
        'formatted_computation_date',
        'is_editable',
    ];

    /*
    |--------------------------------------------------------------------------
    | Boot Method
    |--------------------------------------------------------------------------
    */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->computation_status)) {
                $model->computation_status = self::STATUS_DRAFT;
            }
            if (empty($model->status)) {
                $model->status = true;
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function clientType()
    {
        return $this->belongsTo(ClientType::class);
    }

    public function values()
    {
        return $this->hasMany(ComputationValue::class)->with('head');
    }

    public function tdsEntries()
    {
        return $this->hasMany(ComputationTdsEntry::class)->orderBy('sort_order');
    }

    public function tcsEntries()
    {
        return $this->hasMany(ComputationTcsEntry::class)->orderBy('sort_order');
    }

    public function advanceTaxEntries()
    {
        return $this->hasMany(ComputationAdvanceTaxEntry::class)->orderBy('sort_order');
    }

    public function bankAccounts()
    {
        return $this->hasMany(ComputationBankAccount::class)->orderBy('sort_order');
    }

    public function salaryEntries()
    {
        return $this->hasMany(ComputationSalaryEntry::class)->orderBy('sort_order');
    }

    // ✅ Fixed: removed orderBy('sort_order') because `computation_media` has no sort_order column
    public function media()
    {
        return $this->hasMany(ComputationMedia::class);
    }
public function exemptIncomes()
{
    return $this->hasMany(ComputationExemptIncome::class)
        ->orderBy('sort_order');
}
   public function housePropertyEntries()
{
    return $this->hasMany(
        ComputationHousePropertyEntry::class,
        'computation_id'
    )->orderBy('sort_order');
}


public function capitalGainEntries()
{
    return $this->hasMany(
        ComputationCapitalGainEntry::class,
        'computation_id'
    )->orderBy('sort_order');
}

public function vdaEntries()
{
    return $this->hasMany(
        ComputationVdaEntry::class,
        'computation_id'
    )->orderBy('sort_order');
}

public function otherSourceEntries()
{
    return $this->hasMany(
        ComputationOtherSourceEntry::class,
        'computation_id'
    )->orderBy('sort_order');
}



public function deductionEntries()
{
    return $this->hasMany(
        ComputationDeductionEntry::class,
        'computation_id'
    )->orderBy('sort_order');
}


public function assets()
{
    return $this->hasMany(ComputationAsset::class);
}

public function investments()
{
    return $this->hasMany(ComputationInvestment::class);
}

public function securityDeposits()
{
    return $this->hasMany(ComputationSecurityDeposit::class);
}

public function verification()
{
    return $this->hasOne(
        ComputationVerification::class,
        'computation_id'
    );
}

public function interestEntry()
{
    return $this->hasOne(ComputationInterestEntry::class, 'computation_id');
}

public function selfAssessmentTaxEntries()
{
    return $this->hasMany(ComputationSelfAssessmentTaxEntry::class, 'computation_id')
                ->orderBy('sort_order');
}
public function salesReconciliations()
{
    return $this->hasMany(SalesReconciliation::class);
}

public function registerSalesReconciliation()
{
    return $this->hasOne(SalesReconciliation::class)
        ->where('reconciliation_type', 'register');
}

public function unregisterSalesReconciliation()
{
    return $this->hasOne(SalesReconciliation::class)
        ->where('reconciliation_type', 'unregister');
}

public function debtors()
{
    return $this->hasMany(ComputationDebtor::class, 'computation_id');
}
/*
|--------------------------------------------------------------------------
| Purchase Reconciliation Relationships
|--------------------------------------------------------------------------
*/

public function bankReconciliation()
{
    return $this->hasOne(BankReconciliation::class, 'computation_id');
}

public function purchaseReconciliations()
{
    return $this->hasMany(PurchaseReconciliation::class);
}

public function registerPurchaseReconciliation()
{
    return $this->hasOne(PurchaseReconciliation::class)
        ->where('reconciliation_type', 'register');
}

public function unregisterPurchaseReconciliation()
{
    return $this->hasOne(PurchaseReconciliation::class)
        ->where('reconciliation_type', 'unregister');
}

/*
|--------------------------------------------------------------------------
| Debit / Credit Note Register
|--------------------------------------------------------------------------
*/

public function debitCreditNoteRegisters()
{
    return $this->hasMany(DebitCreditNoteRegister::class);
}

public function debitNoteRegister()
{
    return $this->hasOne(DebitCreditNoteRegister::class)
        ->where('note_type', 'debit');
}

public function creditNoteRegister()
{
    return $this->hasOne(DebitCreditNoteRegister::class)
        ->where('note_type', 'credit');
}
public function debtorReconciliations()
{
    return $this->hasMany(DebtorReconciliation::class, 'computation_id');
}


public function creditorReconciliations()
{
    return $this->hasMany(CreditorReconciliation::class, 'computation_id');
}

public function tradingAccount()
    {
        return $this->hasOne(TradingAccount::class, 'computation_id');
    }
    
    
    public function profitLossAccount()
{
    return $this->hasOne(
        ProfitLossAccount::class,
        'computation_id'
    );
}

public function balanceSheet(): HasOne
{
    return $this->hasOne(BalanceSheet::class, 'computation_id');
}
    // public function houseProperties()
    // {
    //     return $this->hasMany(ComputationHouseProperty::class)->orderBy('sort_order');
    // }

    // public function businessEntries()
    // {
    //     return $this->hasMany(ComputationBusinessEntry::class)->orderBy('sort_order');
    // }

    // public function capitalGainEntries()
    // {
    //     return $this->hasMany(ComputationCapitalGainEntry::class)->orderBy('sort_order');
    // }

    // public function otherSourceEntries()
    // {
    //     return $this->hasMany(ComputationOtherSourceEntry::class)->orderBy('sort_order');
    // }

    // public function deductionEntries()
    // {
    //     return $this->hasMany(ComputationDeductionEntry::class)->orderBy('sort_order');
    // }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */
    public function getTaxRegimeLabelAttribute(): string
    {
        return self::$regimes[$this->tax_regime] ?? ucfirst($this->tax_regime);
    }

    public function getComputationStatusLabelAttribute(): string
    {
        return self::$computationStatuses[$this->computation_status] ?? ucfirst($this->computation_status);
    }

    public function getFilingStatusLabelAttribute(): string
    {
        return self::$filingStatuses[$this->filing_status] ?? ucfirst($this->filing_status);
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return self::$paymentStatuses[$this->payment_status] ?? ucfirst($this->payment_status);
    }

    public function getFormattedPaidAmountAttribute(): string
    {
        $currency = config('app.currency', '₹');
        return $this->paid_amount ? $currency . ' ' . number_format($this->paid_amount, 2) : $currency . ' 0.00';
    }

    public function getFormattedComputationDateAttribute(): string
    {
        return $this->computation_date ? $this->computation_date->format('d M, Y') : '—';
    }

    public function getIsEditableAttribute(): bool
    {
        return ! in_array($this->computation_status, [self::STATUS_FILED, self::STATUS_SUBMITTED]);
    }

    /*
    |--------------------------------------------------------------------------
    | Mutators
    |--------------------------------------------------------------------------
    */
    public function setAlternateMobileAttribute($value)
    {
        $this->attributes['alternate_mobile'] = $value ? preg_replace('/[^0-9]/', '', $value) : null;
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */
    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('computation_status', self::STATUS_DRAFT);
    }

    public function scopeFinal(Builder $query): Builder
    {
        return $query->where('computation_status', self::STATUS_FINAL);
    }

    public function scopeFiled(Builder $query): Builder
    {
        return $query->where('computation_status', self::STATUS_FILED);
    }

    public function scopeSubmitted(Builder $query): Builder
    {
        return $query->where('computation_status', self::STATUS_SUBMITTED);
    }

    public function scopeByYear(Builder $query, string $year): Builder
    {
        return $query->where('assessment_year', $year);
    }

    public function scopeByRegime(Builder $query, string $regime): Builder
    {
        return $query->where('tax_regime', $regime);
    }

    public function scopeByClient(Builder $query, int $clientId): Builder
    {
        return $query->where('client_id', $clientId);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    public function scopePaid(Builder $query): Builder
    {
        return $query->where('payment_status', self::PAYMENT_PAID);
    }

    public function scopeUnpaid(Builder $query): Builder
    {
        return $query->where('payment_status', self::PAYMENT_UNPAID);
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods (State checks & simple updates)
    |--------------------------------------------------------------------------
    */
    public function isDraft(): bool
    {
        return $this->computation_status === self::STATUS_DRAFT;
    }

    public function isFinal(): bool
    {
        return $this->computation_status === self::STATUS_FINAL;
    }

    public function isFiled(): bool
    {
        return $this->computation_status === self::STATUS_FILED;
    }

    public function isSubmitted(): bool
    {
        return $this->computation_status === self::STATUS_SUBMITTED;
    }

    public function isPaid(): bool
    {
        return $this->payment_status === self::PAYMENT_PAID;
    }

    public function isRefundDue(): bool
    {
        return $this->payment_status === self::PAYMENT_REFUND_DUE;
    }

    public function canEdit(): bool
    {
        return $this->is_editable;
    }

    public function canDelete(): bool
    {
        return $this->isDraft();
    }

    // --- State Mutators (with save) ---
    public function markAsFinal(): self
    {
        $this->update(['computation_status' => self::STATUS_FINAL]);
        return $this;
    }

    public function markAsFiled(): self
    {
        $this->update([
            'computation_status' => self::STATUS_FILED,
            'submitted_at' => now(),
        ]);
        return $this;
    }

    public function markAsSubmitted(): self
    {
        $this->update([
            'computation_status' => self::STATUS_SUBMITTED,
            'submitted_at' => now(),
        ]);
        return $this;
    }

    public function markAsPaid(): self
    {
        $this->update(['payment_status' => self::PAYMENT_PAID]);
        return $this;
    }

    public function markAsUnpaid(): self
    {
        $this->update(['payment_status' => self::PAYMENT_UNPAID]);
        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Calculation Helpers – using correct column names
    |--------------------------------------------------------------------------
    */
    public function getTotalTaxLiability(): float
    {
        return (float) $this->values()->sum('numeric_value');
    }

    // ✅ Fixed: TDS total uses 'tds_deducted'
    public function getTotalTds(): float
    {
        return (float) $this->tdsEntries()->sum('tds_deducted');
    }

    // ✅ Fixed: TCS total uses 'tcs_collected'
    public function getTotalTcs(): float
    {
        return (float) $this->tcsEntries()->sum('tcs_collected');
    }

    // ✅ Fixed: Advance Tax total uses 'amount_paid'
    public function getTotalAdvanceTax(): float
    {
        return (float) $this->advanceTaxEntries()->sum('amount_paid');
    }

    public function getTaxPayable(): float
    {
        $liability = $this->getTotalTaxLiability();
        $tds = $this->getTotalTds();
        $tcs = $this->getTotalTcs();
        $advance = $this->getTotalAdvanceTax();
        return max(0, $liability - ($tds + $tcs + $advance));
    }

    public function getDueDate(): ?Carbon
    {
        $year = is_numeric($this->assessment_year)
            ? (int) $this->assessment_year
            : (int) substr($this->assessment_year, 0, 4);

        if ($this->filing_status === self::FILING_BELATED) {
            return Carbon::createFromDate($year, 12, 31);
        }
        return Carbon::createFromDate($year, 7, 31);
    }

    public function isOverdue(): bool
    {
        $due = $this->getDueDate();
        return $due && $due->isPast() && !$this->isFiled();
    }
}