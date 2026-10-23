<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ComputationSection extends Model
{
    use HasFactory;

    protected $table = 'computation_sections';

    protected $fillable = [
        'code',
        'title',
        'schedule_name',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    public function heads()
    {
        return $this->hasMany(ComputationHead::class, 'section_id')
                    ->orderBy('sort_order')
                    ->with('children'); // load full tree
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
}