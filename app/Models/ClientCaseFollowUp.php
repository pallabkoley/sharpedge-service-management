<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientCaseFollowUp extends Model
{
    use HasFactory;

    protected $table = 'client_case_follow_ups';

    protected $fillable = [
        'client_case_id',
        'follow_up_date',
        'note',
        'created_by',
    ];

    protected $casts = [
        'follow_up_date' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Follow-up belongs to a Case
     */
    public function case()
    {
        return $this->belongsTo(
            ClientCase::class,
            'client_case_id'
        );
    }

    /**
     * User who created/logged the follow-up
     */
    public function createdBy()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }
}