<?php
// app/Models/DocumentRequirement.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentRequirement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'service_id',
        'document_master_id',
        'type',
        'status',
        'order',
    ];

    protected $attributes = [
        'status' => 'active',
        'type'   => 'mandatory',
    ];

    protected $casts = [
        'order'  => 'integer',
    ];

    /**
     * Belongs to Service
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }


// public function uploads()
// {
//     return $this->hasMany(ClientDocumentUpload::class);
// }

    /**
     * Belongs to Document Master
     */
   public function documentMaster()
{
    return $this->belongsTo(DocumentMaster::class);
}


    /**
     * Scope: Active
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: Mandatory
     */
    public function scopeMandatory($query)
    {
        return $query->where('type', 'mandatory');
    }

    /**
     * Scope: Optional
     */
    public function scopeOptional($query)
    {
        return $query->where('type', 'optional');
    }
}
