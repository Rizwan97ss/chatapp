<?php

namespace App\Imports;

use App\Models\Transport;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class TransportsImport implements
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
        return new Transport([
            'vehicle_no' => trim($row['vehicle_no'] ?? ''),
            'vehicle' => trim($row['vehicle'] ?? ''),
            'type' => trim($row['type'] ?? 'Bus'),
            'route' => trim($row['route'] ?? ''),
            'driver' => $row['driver'] ?? null,
            'phone' => $row['phone'] ?? null,
            'plate' => trim($row['plate'] ?? ''),
            'capacity' => (int) ($row['capacity'] ?? 0),
            'students' => (int) ($row['students'] ?? 0),
            'status' => $row['status'] ?? 'Active',
            'notes' => $row['notes'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            '*.vehicle_no' => ['required', 'string', 'max:50', Rule::unique('transports', 'vehicle_no')],
            '*.vehicle' => ['required', 'string', 'max:255'],
            '*.type' => ['required', Rule::in(['Bus', 'Van', 'Mini Bus'])],
            '*.route' => ['required', 'string', 'max:255'],
            '*.driver' => ['nullable', 'string', 'max:255'],
            '*.phone' => ['nullable', 'string', 'max:30'],
            '*.plate' => ['required', 'string', 'max:50', Rule::unique('transports', 'plate')],
            '*.capacity' => ['required', 'integer', 'min:0'],
            '*.students' => ['required', 'integer', 'min:0', 'lte:*.capacity'],
            '*.status' => ['required', Rule::in(['Active', 'Maintenance', 'Inactive'])],
            '*.notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            '*.vehicle_no.required' => 'Vehicle ID is required.',
            '*.vehicle_no.unique' => 'Vehicle ID already exists.',
            '*.vehicle.required' => 'Vehicle name is required.',
            '*.type.in' => 'Vehicle type must be Bus, Van, or Mini Bus.',
            '*.route.required' => 'Route is required.',
            '*.plate.required' => 'Plate number is required.',
            '*.plate.unique' => 'Plate number already exists.',
            '*.capacity.required' => 'Capacity is required.',
            '*.students.required' => 'Assigned students is required.',
            '*.students.lte' => 'Assigned students cannot be greater than capacity.',
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