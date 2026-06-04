<?php

// app/Models/LibraryBook.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LibraryBook extends Model
{
    protected $fillable = [
        'book_no',
        'title',
        'author',
        'category',
        'isbn',
        'shelf',
        'copies',
        'status',
        'description',
    ];
    public function issues()
{
    return $this->hasMany(LibraryBookIssue::class);
}
}