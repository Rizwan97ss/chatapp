<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = [
        'subject_id',
        'name',
        'code',
        'class_name',
        'teacher_name',
        'type',
        'weekly_hours',
        'credit',
        'status',
        'description',
    ];
}