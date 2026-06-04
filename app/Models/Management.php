<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Management extends Model
{
    protected $table = 'management';

    protected $fillable = [
        'member_no',
        'name',
        'role',
        'department',
        'email',
        'phone',
        'authority',
        'status',
        'responsibilities',
    ];
}