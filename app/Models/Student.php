<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'admission_no',
        'full_name',
        'email',
        'phone',
        'gender',
        'date_of_birth',
        'class_name',
        'section',
        'parent_name',
        'parent_phone',
        'admission_date',
        'status',
        'address',
    ];
}
