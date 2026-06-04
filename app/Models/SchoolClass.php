<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolClass extends Model
{
    protected $fillable = [
        'class_id',
        'name',
        'section',
        'teacher_name',
        'room',
        'capacity',
        'students_count',
        'status',
        'description',
    ];
}