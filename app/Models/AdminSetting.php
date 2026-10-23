<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'upi_id',
        'qr_code_path',
        'qr_code_url'
    ];

    /**
     * Relationship with Admin User
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Accessor for upi_qr_code (backward compatibility)
     */
    public function getUpiQrCodeAttribute()
    {
        return $this->qr_code_path;
    }

    /**
     * Accessor for QR code URL
     */
    public function getQrCodeUrlAttribute()
    {
        if ($this->qr_code_path) {
            // Remove 'public/' from the beginning if it exists
            $qrPath = str_replace('public/', '', $this->qr_code_path);
            return asset('storage/' . $qrPath);
        }
        return $this->attributes['qr_code_url'] ?? null;
    }

    /**
     * Get the correct QR code path without 'public/' prefix
     */
    public function getCorrectQrCodePathAttribute()
    {
        return str_replace('public/', '', $this->qr_code_path);
    }
}