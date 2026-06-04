<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transport extends Model
{
    protected $fillable = [
        'vehicle_no',
        'vehicle',
        'type',
        'route',
        'driver',
        'phone',
        'plate',
        'capacity',
        'students',
        'status',
        'notes',
    ];
}