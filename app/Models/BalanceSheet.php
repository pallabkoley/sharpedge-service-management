<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BalanceSheet extends Model
{
    use HasFactory;

    protected $table = 'balance_sheets';

    protected $fillable = [
        'computation_id',
        'status',
        'auditor_remarks',
        'prepared_by',
        'updated_by',
    ];

    /**
     * Parent computation.
     */
    public function computation(): BelongsTo
    {
        return $this->belongsTo(Computation::class, 'computation_id');
    }

    /**
     * All balance sheet items.
     */
    public function items(): HasMany
    {
        return $this->hasMany(BalanceSheetItem::class, 'balance_sheet_id')
            ->where('is_active', 1)
            ->orderBy('display_order')
            ->orderBy('id');
    }

    /**
     * All items including inactive rows.
     */
    public function allItems(): HasMany
    {
        return $this->hasMany(BalanceSheetItem::class, 'balance_sheet_id')
            ->orderBy('display_order')
            ->orderBy('id');
    }

    /**
     * Liability items.
     */
    public function liabilityItems(): HasMany
    {
        return $this->hasMany(BalanceSheetItem::class, 'balance_sheet_id')
            ->where('side', 'liability')
            ->where('is_active', 1)
            ->orderBy('display_order')
            ->orderBy('id');
    }

    /**
     * Asset items.
     */
    public function assetItems(): HasMany
    {
        return $this->hasMany(BalanceSheetItem::class, 'balance_sheet_id')
            ->where('side', 'asset')
            ->where('is_active', 1)
            ->orderBy('display_order')
            ->orderBy('id');
    }

    /**
     * Items by section.
     */
    public function sectionItems(string $section): HasMany
    {
        return $this->hasMany(BalanceSheetItem::class, 'balance_sheet_id')
            ->where('section', $section)
            ->where('is_active', 1)
            ->orderBy('display_order')
            ->orderBy('id');
    }

    /**
     * User who prepared the balance sheet.
     */
    public function preparedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }

    /**
     * User who last updated the balance sheet.
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Check whether the balance sheet is finalized.
     */
    public function isFinalized(): bool
    {
        return $this->status === 'finalized';
    }

    /**
     * Check whether the balance sheet is editable.
     */
    public function isEditable(): bool
    {
        return $this->status !== 'finalized';
    }
}