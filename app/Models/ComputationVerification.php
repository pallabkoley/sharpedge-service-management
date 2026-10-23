<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComputationVerification extends Model
{
    use HasFactory;

    /**
     * Table
     */
    protected $table = 'computation_verifications';

    /**
     * Primary Key
     */
    protected $primaryKey = 'id';

    /**
     * Timestamps
     */
    public $timestamps = true;

    /**
     * Mass Assignable
     */
    protected $fillable = [

        'computation_id',

        // Prepared By
        'prepared_by_name',
        'prepared_membership_no',
        'prepared_firm_name',
        'prepared_udin',

        // Verified By
        'verified_by_name',
        'verified_place',
        'verified_date',
        'verified_seal',

        // Assessee Confirmation
        'assessee_name',
        'assessee_pan',
        'assessee_place',
        'assessee_date',

    ];

    /**
     * Casts
     */
    protected $casts = [
        'verified_date' => 'date:Y-m-d',
        'assessee_date' => 'date:Y-m-d',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function computation()
    {
        return $this->belongsTo(Computation::class, 'computation_id');
    }
}