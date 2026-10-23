<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ComputationValue extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Table & Primary Key
    |--------------------------------------------------------------------------
    */
    protected $table = 'computation_values';
    protected $primaryKey = 'id';
    public $timestamps = true;

    /*
    |--------------------------------------------------------------------------
    | Constants (optional)
    |--------------------------------------------------------------------------
    */
    // If you need value types, you can define them here, e.g.:
    // const TYPE_INCOME = 'income';
    // const TYPE_DEDUCTION = 'deduction';
    // public static $types = [...];

    /*
    |--------------------------------------------------------------------------
    | Fillable
    |--------------------------------------------------------------------------
    */
    protected $fillable = [
        'computation_id',
        'head_id',
        'column_key',
        'value',
        'numeric_value',
        'formatted_value',
        'is_overridden',
        'remarks',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */
    protected $casts = [
        'numeric_value'  => 'decimal:2',
        'is_overridden'  => 'boolean',
        'created_at'     => 'datetime',
        'updated_at'     => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Appends (computed attributes)
    |--------------------------------------------------------------------------
    */
    protected $appends = [
        'display_value',
        'head_label',
        'is_editable_label',
        'is_formula_label',
    ];

    /*
    |--------------------------------------------------------------------------
    | Hidden (for API responses)
    |--------------------------------------------------------------------------
    */
    // protected $hidden = []; // none needed usually

    /*
    |--------------------------------------------------------------------------
    | Boot Method
    |--------------------------------------------------------------------------
    */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Default: not overridden (auto-calculated)
            if (is_null($model->is_overridden)) {
                $model->is_overridden = false;
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    public function computation()
    {
        return $this->belongsTo(Computation::class);
    }

    public function head()
    {
        return $this->belongsTo(ComputationHead::class);
    }

    // Optionally, if you track who last modified this value:
    // public function modifier() { return $this->belongsTo(User::class, 'modified_by'); }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */
    public function getDisplayValueAttribute(): string
    {
        // Prefer formatted_value if set, else format numeric, else fallback to value
        if (! empty($this->formatted_value)) {
            return $this->formatted_value;
        }

        if (! is_null($this->numeric_value)) {
            return number_format($this->numeric_value, 2);
        }

        return (string) ($this->value ?? '');
    }

    public function getHeadLabelAttribute(): string
    {
        return $this->head?->name ?? $this->column_key ?? '—';
    }

    public function getIsEditableLabelAttribute(): string
    {
        return $this->isEditable() ? 'Editable' : 'Read-only';
    }

    public function getIsFormulaLabelAttribute(): string
    {
        return $this->isFormula() ? 'Formula' : 'Manual Entry';
    }

    /*
    |--------------------------------------------------------------------------
    | Mutators
    |--------------------------------------------------------------------------
    */
    // Clean numeric_value to ensure proper decimal format
    public function setNumericValueAttribute($value)
    {
        $this->attributes['numeric_value'] = $value !== null ? (float) $value : null;
    }

    // If you want to auto-generate formatted_value from numeric_value:
    // public function setNumericValueAttribute($value)
    // {
    //     $this->attributes['numeric_value'] = $value;
    //     $this->attributes['formatted_value'] = $value !== null ? number_format($value, 2) : null;
    // }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */
    public function scopeByComputation(Builder $query, int $computationId): Builder
    {
        return $query->where('computation_id', $computationId);
    }

    public function scopeByHead(Builder $query, int $headId): Builder
    {
        return $query->where('head_id', $headId);
    }

    public function scopeByColumn(Builder $query, string $columnKey): Builder
    {
        return $query->where('column_key', $columnKey);
    }

    public function scopeManual(Builder $query): Builder
    {
        return $query->where('is_overridden', true);
    }

    public function scopeAuto(Builder $query): Builder
    {
        return $query->where('is_overridden', false);
    }

    public function scopeWithHead(Builder $query): Builder
    {
        return $query->with('head');
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods (State checks & actions)
    |--------------------------------------------------------------------------
    */
    public function isFormula(): bool
    {
        return (bool) ($this->head?->is_formula ?? false);
    }

    public function isEditable(): bool
    {
        // If the head says not overridable, or if it's a formula, we might still allow override?
        // Usually we allow override only if head->allow_override is true.
        return (bool) ($this->head?->allow_override ?? true);
    }

    public function isManual(): bool
    {
        return (bool) $this->is_overridden;
    }

    public function isAutoCalculated(): bool
    {
        return ! $this->isManual();
    }

    public function canEdit(): bool
    {
        return $this->isEditable() && ! $this->computation?->isFiled(); // cannot edit if computation is filed
    }

    // Actions
    public function markAsManual(): self
    {
        $this->update(['is_overridden' => true]);
        return $this;
    }

    public function markAsAuto(): self
    {
        $this->update(['is_overridden' => false]);
        return $this;
    }

    // Get raw numeric value (for calculations)
    public function getRawValue(): ?float
    {
        return $this->numeric_value ?? (is_numeric($this->value) ? (float) $this->value : null);
    }

    // Set value with formatted representation
    public function setValue(float $value, ?string $formatted = null): self
    {
        $this->numeric_value = $value;
        $this->formatted_value = $formatted ?? number_format($value, 2);
        return $this;
    }
}