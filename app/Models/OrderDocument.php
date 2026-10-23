<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDocument extends Model
{
    use HasFactory;

    protected $table = 'order_documents';

    protected $fillable = [
        'order_id',
        'service_id',
        'document_master_id',
        'status',
        'uploaded_by',
        'uploaded_at',
        'verified_by',
        'verified_at',
        'remarks',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    /* =====================
       RELATIONSHIPS
    ====================== */

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function documentMaster()
    {
        return $this->belongsTo(DocumentMaster::class, 'document_master_id');
    }

    public function uploadedBy()
    {
        return $this->belongsTo(Staff::class, 'uploaded_by');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(Staff::class, 'verified_by');
    }

    /* =====================
       MULTIPLE FILES
    ====================== */

    public function files()
    {
        return $this->hasMany(DocumentFile::class, 'order_document_id');
    }

    /* =====================
       ACCESSORS
    ====================== */

    public function getHasFilesAttribute()
    {
        return $this->files()->exists();
    }

    public function getFilesCountAttribute()
    {
        return $this->files()->count();
    }

    public function getTotalFileSizeAttribute()
    {
        return $this->files()->sum('file_size');
    }
}
