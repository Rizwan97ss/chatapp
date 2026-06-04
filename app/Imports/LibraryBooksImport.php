<?php

namespace App\Imports;

use App\Models\LibraryBook;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class LibraryBooksImport implements
    ToModel,
    WithHeadingRow,
    WithValidation,
    WithChunkReading,
    WithBatchInserts,
    SkipsOnFailure
{
    use SkipsFailures;

    public function model(array $row)
    {
        return new LibraryBook([
            'book_no' => trim($row['book_no'] ?? ''),
            'title' => trim($row['title'] ?? ''),
            'author' => $row['author'] ?? null,
            'category' => $row['category'] ?? null,
            'isbn' => $row['isbn'] ?? null,
            'shelf' => $row['shelf'] ?? null,
            'copies' => (int) ($row['copies'] ?? 0),
            'status' => $row['status'] ?? 'Available',
            'description' => $row['description'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            '*.book_no' => [
                'required',
                'string',
                'max:255',
                Rule::unique('library_books', 'book_no'),
            ],
            '*.title' => ['required', 'string', 'max:255'],
            '*.author' => ['nullable', 'string', 'max:255'],
            '*.category' => ['nullable', 'string', 'max:255'],
            '*.isbn' => ['nullable', 'string', 'max:255'],
            '*.shelf' => ['nullable', 'string', 'max:255'],
            '*.copies' => ['required', 'integer', 'min:0'],
            '*.status' => ['required', Rule::in(['Available', 'Issued', 'Overdue'])],
            '*.description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            '*.book_no.required' => 'Book ID is required.',
            '*.book_no.unique' => 'Book ID already exists.',
            '*.title.required' => 'Book title is required.',
            '*.copies.required' => 'Copies field is required.',
            '*.copies.integer' => 'Copies must be a number.',
            '*.status.in' => 'Status must be Available, Issued, or Overdue.',
        ];
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function batchSize(): int
    {
        return 500;
    }
}