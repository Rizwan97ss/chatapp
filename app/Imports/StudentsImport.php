<?php

namespace App\Imports;

use App\Models\Student;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Validators\Failure;

class StudentsImport implements
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
        return new Student([
            'admission_no' => trim($row['admission_no'] ?? ''),
            'full_name' => trim($row['full_name'] ?? ''),
            'email' => $row['email'] ?? null,
            'phone' => $row['phone'] ?? null,
            'gender' => $row['gender'] ?? null,
            'date_of_birth' => $row['date_of_birth'] ?? null,
            'class_name' => trim($row['class_name'] ?? ''),
            'section' => $row['section'] ?? null,
            'parent_name' => trim($row['parent_name'] ?? ''),
            'parent_phone' => trim($row['parent_phone'] ?? ''),
            'admission_date' => $row['admission_date'] ?? null,
            'status' => $row['status'] ?? 'Active',
            'address' => $row['address'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            '*.admission_no' => [
                'required',
                'string',
                'max:50',
                Rule::unique('students', 'admission_no'),
            ],

            '*.full_name' => ['required', 'string', 'max:255'],
            '*.email' => ['nullable', 'email', 'max:255', Rule::unique('students', 'email')],
            '*.phone' => ['nullable', 'string', 'max:30'],

            '*.gender' => ['nullable', Rule::in(['Male', 'Female'])],
            '*.date_of_birth' => ['nullable', 'date', 'before:today'],

            '*.class_name' => ['required', 'string', 'max:100'],
            '*.section' => ['nullable', 'string', 'max:20'],

            '*.parent_name' => ['required', 'string', 'max:255'],
            '*.parent_phone' => ['required', 'string', 'max:30'],

            '*.admission_date' => ['nullable', 'date'],
            '*.status' => ['required', Rule::in(['Active', 'Pending', 'Inactive'])],

            '*.address' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            '*.admission_no.required' => 'Admission number is required.',
            '*.admission_no.unique' => 'Admission number already exists.',
            '*.full_name.required' => 'Full name is required.',
            '*.class_name.required' => 'Class is required.',
            '*.parent_name.required' => 'Parent name is required.',
            '*.parent_phone.required' => 'Parent phone is required.',
            '*.email.email' => 'Email must be valid.',
            '*.status.in' => 'Status must be Active, Pending, or Inactive.',
            '*.gender.in' => 'Gender must be Male or Female.',
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