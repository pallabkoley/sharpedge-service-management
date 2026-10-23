<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientType extends Model
{
    protected $fillable = [
        'name',
        'code',
        'status'
    ];

    public function groups()
    {
        return $this->hasMany(ComputationGroup::class);
    }

    public function computations()
    {
        return $this->hasMany(Computation::class);
    }
}