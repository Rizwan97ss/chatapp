<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admission extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_no',
        'student_name',
        'email',
        'phone',
        'gender',
        'date_of_birth',
        'applied_class',
        'previous_school',
        'parent_name',
        'parent_phone',
        'parent_email',
        'application_date',
        'status',
        'address',
        'remarks',
    ];
}