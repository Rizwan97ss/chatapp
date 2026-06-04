<?php

namespace App\Imports;

use App\Models\Hostel;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class HostelsImport implements
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
        return new Hostel([
            'hostel_no' => trim($row['hostel_no'] ?? ''),
            'name' => trim($row['name'] ?? ''),
            'type' => trim($row['type'] ?? 'Boys'),
            'warden' => $row['warden'] ?? null,
            'location' => $row['location'] ?? null,
            'rooms' => (int) ($row['rooms'] ?? 0),
            'capacity' => (int) ($row['capacity'] ?? 0),
            'occupied' => (int) ($row['occupied'] ?? 0),
            'status' => $row['status'] ?? 'Active',
            'notes' => $row['notes'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            '*.hostel_no' => ['required', 'string', 'max:50', Rule::unique('hostels', 'hostel_no')],
            '*.name' => ['required', 'string', 'max:255'],
            '*.type' => ['required', Rule::in(['Boys', 'Girls', 'Staff / Guest'])],
            '*.warden' => ['nullable', 'string', 'max:255'],
            '*.location' => ['nullable', 'string', 'max:255'],
            '*.rooms' => ['required', 'integer', 'min:0'],
            '*.capacity' => ['required', 'integer', 'min:0'],
            '*.occupied' => ['required', 'integer', 'min:0', 'lte:*.capacity'],
            '*.status' => ['required', Rule::in(['Active', 'Maintenance', 'Inactive'])],
            '*.notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            '*.hostel_no.required' => 'Hostel ID is required.',
            '*.hostel_no.unique' => 'Hostel ID already exists.',
            '*.name.required' => 'Hostel name is required.',
            '*.type.in' => 'Type must be Boys, Girls, or Staff / Guest.',
            '*.rooms.required' => 'Rooms is required.',
            '*.capacity.required' => 'Capacity is required.',
            '*.occupied.required' => 'Occupied beds is required.',
            '*.occupied.lte' => 'Occupied beds cannot be greater than capacity.',
            '*.status.in' => 'Status must be Active, Maintenance, or Inactive.',
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