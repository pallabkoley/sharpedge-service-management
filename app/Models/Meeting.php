<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Client;
use App\Models\User;

class Meeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'manual_client_name',

        'requested_by',
        'approved_by',
        'approved_at',

        'meeting_type',
        'purpose',
        'meeting_date',
        'meeting_time',

        'status',
    ];

    protected $casts = [
        'meeting_date' => 'date',
        'meeting_time' => 'string',
        'approved_at'  => 'datetime',
        'status'       => 'string',
    ];

    /* =========================
       RELATIONSHIPS
    ========================= */

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /* =========================
       ACCESSORS
    ========================= */

    // Safe client display (client OR manual)
    public function getClientDisplayNameAttribute()
    {
        return $this->client
            ? $this->client->name
            : $this->manual_client_name;
    }

    /* =========================
       STATUS HELPERS (VERY IMPORTANT)
    ========================= */

    public function isPending(): bool
    {
        return $this->status === 'pending_approval';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /* =========================
       ACTION HELPERS (OPTIONAL BUT CLEAN)
    ========================= */

    public function approve(int $userId): void
    {
        $this->update([
            'status'      => 'approved',
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);
    }

    public function reject(int $userId): void
    {
        $this->update([
            'status'      => 'rejected',
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);
    }

    public function complete(): void
    {
        $this->update([
            'status' => 'completed',
        ]);
    }
}