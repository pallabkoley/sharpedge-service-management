<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ComputationHead extends Model
{
    use HasFactory;

    protected $table = 'computation_heads';

    protected $fillable = [
        'section_id',
        'parent_head_id',
        'row_no',
        'title',
        'code',
        'note_section',
        'row_type',
        'input_type',
        'formula',
        'is_formula',
        'is_editable',
        'is_visible',
        'allow_override',
        'decimal_places',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'is_formula'     => 'boolean',
        'is_editable'    => 'boolean',
        'is_visible'     => 'boolean',
        'allow_override' => 'boolean',
        'status'         => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    public function section()
    {
        return $this->belongsTo(ComputationSection::class);
    }

    public function parent()
    {
        return $this->belongsTo(ComputationHead::class, 'parent_head_id');
    }

    public function children()
    {
        return $this->hasMany(ComputationHead::class, 'parent_head_id')
                    ->orderBy('sort_order')
                    ->with('children'); // eager load recursively
    }

    public function values()
    {
        return $this->hasMany(ComputationValue::class, 'head_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_visible', true);
    }

    public function scopeFormula(Builder $query): Builder
    {
        return $query->where('is_formula', true);
    }

    public function scopeInput(Builder $query): Builder
    {
        return $query->where('is_formula', false);
    }

    public function scopeParent(Builder $query): Builder
    {
        return $query->whereNull('parent_head_id');
    }

    public function scopeChild(Builder $query): Builder
    {
        return $query->whereNotNull('parent_head_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */
    public function hasChildren(): bool
    {
        return $this->children()->exists();
    }

    public function isParent(): bool
    {
        return $this->parent_head_id === null;
    }

    public function isChild(): bool
    {
        return $this->parent_head_id !== null;
    }
}