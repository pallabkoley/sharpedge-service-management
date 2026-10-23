<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientCustomFieldValue extends Model
{
    protected $table = 'client_custom_field_values';

    protected $fillable = [
        'client_id',
        'custom_field_id',
        'field_value',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function customField()
    {
        return $this->belongsTo(ClientCustomField::class, 'custom_field_id');
    }
}