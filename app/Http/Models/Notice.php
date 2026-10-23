<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    use HasFactory;

    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'title',
        'message',
        'target_role',
        'status',
        'start_date',
        'end_date',
        'created_by',
    ];

    /**
     * Cast attributes
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    /**
     * Relationship: Notice creator (Admin)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope: only active notices
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: visible for a given role
     */
    public function scopeForRole($query, $role)
    {
        return $query->where(function ($q) use ($role) {
            $q->where('target_role', $role)
              ->orWhere('target_role', 'all');
        });
    }

    /**
     * Scope: currently valid notices (date-based)
     */
    public function scopeCurrentlyVisible($query)
    {
        return $query->whereDate('start_date', '<=', now())
            ->where(function ($q) {
                $q->whereNull('end_date')
                  ->orWhereDate('end_date', '>=', now());
            });
    }
}
