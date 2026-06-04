<?php

// app/Http/Requests/UpdateLibraryBookRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLibraryBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $bookId = $this->route('libraryBook')?->id;

        return [
            'book_no' => [
                'required',
                'string',
                'max:255',
                Rule::unique('library_books', 'book_no')->ignore($bookId),
            ],
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
