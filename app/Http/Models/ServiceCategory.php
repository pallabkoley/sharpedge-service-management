<?php
// app/Models/ServiceCategory.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_code',
        'name',
        'slug',
        'status',
        'services_count',
        'display_order',
    ];

    protected $casts = [
        'services_count' => 'integer',
        'display_order'  => 'integer',
    ];

    /**
     * Relationship with Services
     */
    public function services()
    {
        return $this->hasMany(Service::class);
    }

    /**
     * Scope for active categories
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for inactive categories
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Scope for ordered categories
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order');
    }

    /**
     * Update services count
     */
    public function updateServicesCount()
    {
        $this->update([
            'services_count' => $this->services()->count(),
        ]);
    }
}
