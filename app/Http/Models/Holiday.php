<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    use HasFactory;

    protected $table = 'holidays';

    protected $fillable = [
        'date',
        'title',
        'type',
        'is_default',
        'color',
    ];

    protected $casts = [
        'date'       => 'date',
        'is_default' => 'boolean',
    ];
}
