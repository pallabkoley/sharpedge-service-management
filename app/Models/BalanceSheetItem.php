<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BalanceSheetItem extends Model
{
    use HasFactory;

    protected $table = 'balance_sheet_items';

    protected $fillable = [
        'balance_sheet_id',
        'side',
        'section',
        'parent_id',
        'item_key',
        'code',
        'label',
        'value_type',
        'source_type',
        'source_key',
        'amount',
        'is_system',
        'is_editable',
        'is_active',
        'display_order',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_system' => 'boolean',
        'is_editable' => 'boolean',
        'is_active' => 'boolean',
        'display_order' => 'integer',
    ];

    /**
     * Parent balance sheet.
     */
    public function balanceSheet(): BelongsTo
    {
        return $this->belongsTo(BalanceSheet::class, 'balance_sheet_id');
    }

    /**
     * Parent item for hierarchical rows.
     *
     * Example:
     * Building
     *   └── Less: Depreciation
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Child items.
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')
            ->where('is_active', 1)
            ->orderBy('display_order')
            ->orderBy('id');
    }

    /**
     * Created by user.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Updated by user.
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Check if this is a manual item.
     */
    public function isManual(): bool
    {
        return $this->value_type === 'manual';
    }

    /**
     * Check if this is a source item.
     */
    public function isSource(): bool
    {
        return $this->value_type === 'source';
    }

    /**
     * Check if this is a calculated item.
     */
    public function isCalculated(): bool
    {
        return $this->value_type === 'calculated';
    }

    /**
     * Check whether user can edit this item.
     */
    public function canEdit(): bool
    {
        return (bool) $this->is_editable;
    }

    /**
     * Scope for manual entries.
     */
    public function scopeManual($query)
    {
        return $query->where('value_type', 'manual');
    }

    /**
     * Scope for source-based entries.
     */
    public function scopeSource($query)
    {
        return $query->where('value_type', 'source');
    }

    /**
     * Scope for calculated entries.
     */
    public function scopeCalculated($query)
    {
        return $query->where('value_type', 'calculated');
    }

    /**
     * Scope for assets.
     */
    public function scopeAssets($query)
    {
        return $query->where('side', 'asset');
    }

    /**
     * Scope for liabilities.
     */
    public function scopeLiabilities($query)
    {
        return $query->where('side', 'liability');
    }
}