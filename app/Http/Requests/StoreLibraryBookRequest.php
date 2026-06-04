<?php

// app/Http/Requests/StoreLibraryBookRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLibraryBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'book_no' => ['required', 'string', 'max:255', 'unique:library_books,book_no'],
            'title' => ['required', 'string', 'max:255'],
            'author' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'isbn' => ['nullable', 'string', 'max:255'],
            'shelf' => ['nullable', 'string', 'max:255'],
            'copies' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:Available,Issued,Overdue'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }
}