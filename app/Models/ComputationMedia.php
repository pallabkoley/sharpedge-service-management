<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComputationMedia extends Model
{
    use HasFactory;

    protected $fillable = [
        'computation_id',
        'type',
        'file_path',
    ];

    public function computation()
    {
        return $this->belongsTo(Computation::class);
    }
}