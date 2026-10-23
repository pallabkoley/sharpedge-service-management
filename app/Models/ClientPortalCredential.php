<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientPortalCredential extends Model
{
    use HasFactory;

    protected $table = 'client_portal_credentials';

    protected $fillable = [
        'client_id',
        'portal',
        'other_portal_name',
        'login_username',
        'password',
        'notes',
    ];

    protected $casts = [
        'password' => 'encrypted',
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