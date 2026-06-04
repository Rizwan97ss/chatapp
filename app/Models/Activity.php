<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = [
        'activity_no',
        'title',
        'type',
        'class_name',
        'activity_date',
        'activity_time',
        'venue',
        'organizer',
        'status',
        'description',
    ];

    protected $casts = [
        'activity_date' => 'date',
    ];
}