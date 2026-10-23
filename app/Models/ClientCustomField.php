<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientCustomField extends Model
{
    protected $table = 'client_custom_fields';

    protected $fillable = [
        'field_name',
        'field_label',
        'field_type',
        'options',
        'is_required',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];
}