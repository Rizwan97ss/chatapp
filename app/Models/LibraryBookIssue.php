<?php

// app/Models/LibraryBookIssue.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LibraryBookIssue extends Model
{
   protected $fillable = [
    'library_book_id',
    'student_name',
    'admission_no',
    'class_name',
    'issue_date',
    'due_date',
    'return_date',
    'status',
    'notes',

    'late_fee',
    'lost_fee',
    'broken_fee',
    'total_fee',
    'fee_status',
    'payment_method',
    'return_notes',
];
    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'return_date' => 'date',
    ];

    public function book()
    {
        return $this->belongsTo(LibraryBook::class, 'library_book_id');
    }
}