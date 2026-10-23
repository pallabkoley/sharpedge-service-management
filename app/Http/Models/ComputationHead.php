<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComputationHead extends Model
{
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

        'is_formula' => 'boolean',
        'is_editable' => 'boolean',
        'is_visible' => 'boolean',
        'allow_override' => 'boolean',
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
        return $this->hasMany(ComputationHead::class, 'parent_head_id');
    }

    public function values()
    {
        return $this->hasMany(ComputationValue::class, 'head_id');
    }
}