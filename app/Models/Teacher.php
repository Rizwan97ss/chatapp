<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable = [
        'teacher_id',
        'full_name',
        'email',
        'phone',
        'gender',
        'date_of_birth',
        'subject',
        'assigned_class',
        'joining_date',
        'status',
        'address',
    ];
}