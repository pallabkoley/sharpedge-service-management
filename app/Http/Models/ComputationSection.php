<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComputationSection extends Model
{
    protected $fillable = [

        'code',
        'title',
        'schedule_name',
        'sort_order',
        'status',
    ];

    public function heads()
    {
        return $this->hasMany(ComputationHead::class, 'section_id');
    }
}