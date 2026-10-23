<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientTurnover extends Model
{
    use HasFactory;

    protected $table = 'client_turnovers';

    protected $fillable = [
        'client_id',
        'financial_year',
        'month',
        'amount',
    ];

    protected $casts = [
        'month' => 'integer',
        'amount' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
}