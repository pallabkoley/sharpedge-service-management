<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    use HasFactory;

    protected $table = 'documents';

    protected $fillable = [
        'client_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'visibility',
        'uploaded_by'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getFileUrlAttribute()
    {
        return asset('storage/' . $this->file_path);
    }

    public function getReadableSizeAttribute()
    {
        if (!$this->file_size) return null;

        if ($this->file_size < 1024) {
            return $this->file_size . ' B';
        }

        if ($this->file_size < 1048576) {
            return round($this->file_size / 1024, 2) . ' KB';
        }

        return round($this->file_size / 1048576, 2) . ' MB';
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeClientVisible($query)
    {
        return $query->where('visibility', 'client_visible');
    }

    public function scopeInternal($query)
    {
        return $query->where('visibility', 'internal');
    }

    /*
    |--------------------------------------------------------------------------
    | Auto Delete File
    |--------------------------------------------------------------------------
    */

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($document) {
            if ($document->file_path) {
                Storage::disk('public')->delete($document->file_path);
            }
        });
    }
}