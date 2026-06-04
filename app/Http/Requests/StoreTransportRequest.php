<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTransportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vehicle_no' => [
                'required',
                'string',
                'max:50',
                'unique:transports,vehicle_no',
                'regex:/^[A-Za-z0-9\-\/_]+$/',
            ],
            'vehicle' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z0-9\s.\'-]+$/'],
            'type' => ['required', Rule::in(['Bus', 'Van', 'Mini Bus'])],
            'route' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z0-9\s.\-\/]+$/'],
            'driver' => ['nullable', 'string', 'max:255', 'regex:/^[A-Za-z\s.\'-]+$/'],
            'phone' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+\-\s()]+$/'],
            'plate' => [
                'required',
                'string',
                'max:50',
                'unique:transports,plate',
                'regex:/^[A-Za-z0-9\-\/_]+$/',
            ],
            'capacity' => ['required', 'integer', 'min:0'],
            'students' => ['required', 'integer', 'min:0', 'lte:capacity'],
            'status' => ['required', Rule::in(['Active', 'Maintenance', 'Inactive'])],
            'notes' => ['nullable', 'string', 'max:1000','regex:/^[A-Za-z0-9\s.\-\/]+$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'vehicle_no.required' => 'Vehicle ID is required.',
            'vehicle_no.unique' => 'Vehicle ID already exists.',
            'vehicle_no.regex' => 'Vehicle ID may only contain letters, numbers, hyphen, slash, and underscore.',

            'vehicle.required' => 'Vehicle name is required.',
            'vehicle.regex' => 'Vehicle name contains invalid characters.',

            'type.required' => 'Vehicle type is required.',
            'type.in' => 'Vehicle type must be Bus, Van, or Mini Bus.',

            'route.required' => 'Route is required.',
            'route.regex' => 'Route contains invalid characters.',

            'driver.regex' => 'Driver name contains invalid characters.',

            'phone.regex' => 'Phone may only contain numbers, spaces, plus sign, hyphen, and brackets.',

            'plate.required' => 'Plate number is required.',
            'plate.unique' => 'Plate number already exists.',
            'plate.regex' => 'Plate number may only contain letters, numbers, hyphen, slash, and underscore.',

            'capacity.required' => 'Capacity is required.',
            'capacity.integer' => 'Capacity must be a number.',

            'students.required' => 'Assigned students is required.',
            'students.integer' => 'Assigned students must be a number.',
            'students.lte' => 'Assigned students cannot be greater than total capacity.',

            'status.required' => 'Status is required.',
            'status.in' => 'Status must be Active, Maintenance, or Inactive.',

            'notes.max' => 'Notes must not be greater than 1000 characters.',
            'notes.regex' => 'Notes contains invalid characters.',
        ];
    }
}