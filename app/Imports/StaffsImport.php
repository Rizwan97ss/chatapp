<?php

namespace App\Imports;

use App\Models\Staff;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class StaffsImport implements
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
        return new Staff([
            'staff_no' => trim($row['staff_no'] ?? ''),
            'full_name' => trim($row['full_name'] ?? ''),
            'role' => trim($row['role'] ?? ''),
            'department' => trim($row['department'] ?? ''),
            'phone' => trim($row['phone'] ?? ''),
            'email' => $row['email'] ?? null,
            'shift' => $row['shift'] ?? 'Morning',
            'salary' => (float) ($row['salary'] ?? 0),
            'joining_date' => $row['joining_date'] ?? null,
            'status' => $row['status'] ?? 'Active',
            'address' => $row['address'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            '*.staff_no' => [
                'required',
                'string',
                'max:50',
                Rule::unique('staff', 'staff_no'),
                'regex:/^[A-Za-z0-9\-\/_]+$/',
            ],
            '*.full_name' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z\s.\'-]+$/'],
            '*.role' => ['required', 'string', 'max:100', 'regex:/^[A-Za-z\s\/\-]+$/'],
            '*.department' => ['required', 'string', 'max:100', 'regex:/^[A-Za-z\s\/\-]+$/'],
            '*.phone' => ['required', 'string', 'max:30', 'regex:/^[0-9+\s\-()]+$/'],
            '*.email' => ['nullable', 'email', 'max:255', Rule::unique('staff', 'email')],
            '*.shift' => ['required', Rule::in(['Morning', 'Evening', 'Night'])],
            '*.salary' => ['required', 'numeric', 'min:0'],
            '*.joining_date' => ['nullable', 'date'],
            '*.status' => ['required', Rule::in(['Active', 'On Leave', 'Inactive'])],
            '*.address' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            '*.staff_no.required' => 'Staff ID is required.',
            '*.staff_no.unique' => 'Staff ID already exists.',
            '*.staff_no.regex' => 'Staff ID may only contain letters, numbers, hyphen, slash, and underscore.',
            '*.full_name.required' => 'Full name is required.',
            '*.role.required' => 'Role is required.',
            '*.department.required' => 'Department is required.',
            '*.phone.required' => 'Phone number is required.',
            '*.email.email' => 'Email must be valid.',
            '*.email.unique' => 'Email already exists.',
            '*.shift.in' => 'Shift must be Morning, Evening, or Night.',
            '*.salary.required' => 'Salary is required.',
            '*.salary.numeric' => 'Salary must be a valid number.',
            '*.status.in' => 'Status must be Active, On Leave, or Inactive.',
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