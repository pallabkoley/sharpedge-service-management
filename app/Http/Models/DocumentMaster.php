<?php
// app/Models/DocumentMaster.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentMaster extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'document_masters';

    protected $fillable = [
        'document_name',
        'code',
        'file_type',
        'description',
        'status',
    ];

    protected $attributes = [
        'status' => 'active',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    /**
     * One Document can be used in many Services
     */
    public function requirements()
    {
        return $this->hasMany(DocumentRequirement::class, 'document_master_id');
    }
    
public function orderDocuments()
{
    return $this->hasMany(OrderDocument::class, 'document_master_id');
}





    /**
     * Scope: Active documents
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
