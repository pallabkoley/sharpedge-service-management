<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientService extends Model
{
    use HasFactory;

    protected $table = 'client_services';

    protected $fillable = [
        'client_id',
        'service_id',
        'status',
        'assigned_date',
        'start_date',
        'end_date',
        'created_by',
    ];

    protected $casts = [
        'assigned_date' => 'date',
        'start_date'    => 'date',
        'end_date'      => 'date',
    ];

    /**
     * Client relation
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Service relation
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Admin / Staff who assigned the service
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
