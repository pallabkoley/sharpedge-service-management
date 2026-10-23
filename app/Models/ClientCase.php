<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientCase extends Model
{
    use HasFactory;

    protected $table = 'client_cases';

    protected $fillable = [
        'client_id',
        'case_type',
        'other_case_type',
        'financial_year',
        'reference_notice_din',
        'issuing_authority_office',
        'status',
        'next_follow_up_date',
        'amount_involved',
        'case_description',
    ];

    protected $casts = [
        'next_follow_up_date' => 'date',
        'amount_involved' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Case belongs to a client
     */
    public function client()
    {
        return $this->belongsTo(
            Client::class,
            'client_id'
        );
    }

    /**
     * Case Follow-up Logs
     */
    public function followUps()
    {
        return $this->hasMany(
            ClientCaseFollowUp::class,
            'client_case_id'
        )->latest('follow_up_date');
    }
}