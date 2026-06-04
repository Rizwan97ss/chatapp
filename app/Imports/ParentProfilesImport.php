<?php

namespace App\Imports;

use App\Models\ParentProfile;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class ParentProfilesImport implements
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
        return new ParentProfile([
            'parent_no' => trim($row['parent_no'] ?? ''),
            'full_name' => trim($row['full_name'] ?? ''),
            'relation' => trim($row['relation'] ?? ''),
            'student_name' => trim($row['student_name'] ?? ''),
            'class_name' => trim($row['class_name'] ?? ''),
            'phone' => trim($row['phone'] ?? ''),
            'email' => $row['email'] ?? null,
            'occupation' => $row['occupation'] ?? null,
            'status' => $row['status'] ?? 'Active',
            'address' => $row['address'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            '*.parent_no' => ['required', 'string', 'max:50', Rule::unique('parent_profiles', 'parent_no')],
            '*.full_name' => ['required', 'string', 'max:255'],
            '*.relation' => ['required', Rule::in(['Father', 'Mother', 'Guardian'])],
            '*.student_name' => ['required', 'string', 'max:255'],
            '*.class_name' => ['required', 'string', 'max:100'],
            '*.phone' => ['required', 'string', 'max:30'],
            '*.email' => ['nullable', 'email', 'max:255', Rule::unique('parent_profiles', 'email')],
            '*.occupation' => ['nullable', 'string', 'max:255'],
            '*.status' => ['required', Rule::in(['Active', 'Pending', 'Inactive'])],
            '*.address' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            '*.parent_no.required' => 'Parent ID is required.',
            '*.parent_no.unique' => 'Parent ID already exists.',
            '*.full_name.required' => 'Full name is required.',
            '*.relation.in' => 'Relation must be Father, Mother, or Guardian.',
            '*.student_name.required' => 'Student name is required.',
            '*.class_name.required' => 'Class is required.',
            '*.phone.required' => 'Phone number is required.',
            '*.email.email' => 'Email must be valid.',
            '*.email.unique' => 'Email already exists.',
            '*.status.in' => 'Status must be Active, Pending, or Inactive.',
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