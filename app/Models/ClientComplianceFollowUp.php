<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientComplianceFollowUp extends Model
{
    use HasFactory;

    protected $table = 'client_compliance_follow_ups';

    protected $fillable = [
        'client_compliance_id',
        'follow_up_date',
        'note',
        'created_by',
    ];

    protected $casts = [
        'follow_up_date' => 'date',
    ];

    public function compliance()
    {
        return $this->belongsTo(
            ClientCompliance::class,
            'client_compliance_id'
        );
    }

    public function createdBy()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }
}