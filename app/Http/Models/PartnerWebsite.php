<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerWebsite extends Model
{
    protected $table = 'partner_websites';

    protected $fillable = [
        'name',
        'logo',
        'url',
        'is_active',
        'display_order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
    public function scopeActive($query)
{
    return $query->where('is_active', 1);
}
}