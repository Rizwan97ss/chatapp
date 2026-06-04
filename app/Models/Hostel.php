<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hostel extends Model
{
    protected $fillable = [
        'hostel_no',
        'name',
        'type',
        'warden',
        'location',
        'rooms',
        'capacity',
        'occupied',
        'status',
        'notes',
    ];
}