<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientDocumentUpload extends Model
{
    use HasFactory;

    protected $table = 'client_document_uploads';

    protected $fillable = [
        'client_id',
        'service_id',
        'document_id',
        'file_path',
        'original_file_name',
        'file_size',
        'status',
        'uploaded_by',
        'uploaded_at',
        'rejected_reason',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    /* =========================
       Relationships
    ========================== */

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function document()
    {
        return $this->belongsTo(DocumentMaster::class, 'document_id');
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'uploaded_by');
    }
}
