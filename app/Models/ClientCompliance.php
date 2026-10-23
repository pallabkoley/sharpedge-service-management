<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientCompliance extends Model
{
    use HasFactory;

    protected $table = 'client_compliances';

    protected $fillable = [
        'client_id',
        'compliance_type',
        'period',
        'due_date',
        'status',
        'notes',
        'repeat_automatically',
        'parent_compliance_id',
    ];

    protected $casts = [
        'due_date' => 'date',
        'repeat_automatically' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
public function followUps()
{
    return $this->hasMany(
        ClientComplianceFollowUp::class,
        'client_compliance_id'
    )->latest('follow_up_date');
}
    // Compliance belongs to Client
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    // Parent compliance
    public function parentCompliance()
    {
        return $this->belongsTo(
            ClientCompliance::class,
            'parent_compliance_id'
        );
    }

    // Child compliances
    public function childCompliances()
    {
        return $this->hasMany(
            ClientCompliance::class,
            'parent_compliance_id'
        );
    }
}