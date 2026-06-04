<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParentProfile extends Model
{
    protected $fillable = [
        'parent_no',
        'full_name',
        'relation',
        'student_name',
        'class_name',
        'phone',
        'email',
        'occupation',
        'status',
        'address',
    ];
}