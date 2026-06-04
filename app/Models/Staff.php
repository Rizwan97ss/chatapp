<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $table = 'staff';

    protected $fillable = [
        'staff_no',
        'full_name',
        'role',
        'department',
        'phone',
        'email',
        'shift',
        'salary',
        'joining_date',
        'status',
        'address',
    ];

    protected $casts = [
        'joining_date' => 'date',
        'salary' => 'decimal:2',
    ];
}