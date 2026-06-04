<?php

namespace App\Imports;

use App\Models\Management;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class ManagementImport implements
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
        return new Management([
            'member_no' => trim($row['member_no'] ?? ''),
            'name' => trim($row['name'] ?? ''),
            'role' => trim($row['role'] ?? ''),
            'department' => trim($row['department'] ?? ''),
            'email' => trim($row['email'] ?? ''),
            'phone' => trim($row['phone'] ?? ''),
            'authority' => $row['authority'] ?? null,
            'status' => $row['status'] ?? 'Active',
            'responsibilities' => $row['responsibilities'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            '*.member_no' => ['required', 'string', 'max:50', Rule::unique('management', 'member_no')],
            '*.name' => ['required', 'string', 'max:255'],
            '*.role' => [
                'required',
                Rule::in([
                    'Principal',
                    'Vice Principal',
                    'Academic Director',
                    'Finance Director',
                    'HR & Admin Head',
                    'Board Member',
                ]),
            ],
            '*.department' => [
                'required',
                Rule::in([
                    'Principal Office',
                    'Academic Affairs',
                    'Finance',
                    'Human Resources',
                    'Board',
                    'Administration',
                ]),
            ],
            '*.email' => ['required', 'email', 'max:255', Rule::unique('management', 'email')],
            '*.phone' => ['nullable', 'string', 'max:30'],
            '*.authority' => ['nullable', 'string', 'max:255'],
            '*.status' => ['required', Rule::in(['Active', 'Inactive'])],
            '*.responsibilities' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            '*.member_no.required' => 'Member ID is required.',
            '*.member_no.unique' => 'Member ID already exists.',
            '*.name.required' => 'Full name is required.',
            '*.role.required' => 'Role is required.',
            '*.role.in' => 'Role must be Principal, Vice Principal, Academic Director, Finance Director, HR & Admin Head, or Board Member.',
            '*.department.required' => 'Department is required.',
            '*.department.in' => 'Department must be Principal Office, Academic Affairs, Finance, Human Resources, Board, or Administration.',
            '*.email.required' => 'Email is required.',
            '*.email.email' => 'Email must be valid.',
            '*.email.unique' => 'Email already exists.',
            '*.status.required' => 'Status is required.',
            '*.status.in' => 'Status must be Active or Inactive.',
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