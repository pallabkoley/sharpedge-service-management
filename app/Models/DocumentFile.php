<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentFile extends Model
{
    use HasFactory;

    protected $table = 'document_files';

    protected $fillable = [
        'order_document_id',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'uploaded_by',
        'uploaded_at',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    /* =====================
       RELATIONSHIPS
    ====================== */

    public function orderDocument()
    {
        return $this->belongsTo(OrderDocument::class, 'order_document_id');
    }

    public function uploadedBy()
    {
        return $this->belongsTo(Staff::class, 'uploaded_by');
    }
}
