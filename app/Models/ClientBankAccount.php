<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientBankAccount extends Model
{
    use HasFactory;

    protected $table = 'client_bank_accounts';

    protected $fillable = [
        'client_id',
        'bank_name',
        'account_number',
        'ifsc_code',
        'account_type',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Bank account belongs to a client
     */
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
}