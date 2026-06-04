<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreHostelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'hostel_no' => [
                'required',
                'string',
                'max:50',
                'unique:hostels,hostel_no',
                'regex:/^[A-Za-z0-9\-\/_]+$/',
            ],
            'name' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z0-9\s.\'-]+$/'],
            'type' => ['required', Rule::in(['Boys', 'Girls', 'Staff / Guest'])],
            'warden' => ['nullable', 'string', 'max:255', 'regex:/^[A-Za-z\s.\'-]+$/'],
            'location' => ['nullable', 'string', 'max:255', 'regex:/^[A-Za-z0-9\s.\-\/]+$/'],
            'rooms' => ['required', 'integer', 'min:0'],
            'capacity' => ['required', 'integer', 'min:0'],
            'occupied' => ['required', 'integer', 'min:0', 'lte:capacity'],
            'status' => ['required', Rule::in(['Active', 'Maintenance', 'Inactive'])],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'hostel_no.required' => 'Hostel ID is required.',
            'hostel_no.unique' => 'Hostel ID already exists.',
            'hostel_no.regex' => 'Hostel ID may only contain letters, numbers, hyphen, slash, and underscore.',
            'name.required' => 'Hostel name is required.',
            'name.regex' => 'Hostel name contains invalid characters.',
            'type.required' => 'Hostel type is required.',
            'type.in' => 'Type must be Boys, Girls, or Staff / Guest.',
            'warden.regex' => 'Warden name contains invalid characters.',
            'location.regex' => 'Location contains invalid characters.',
            'rooms.required' => 'Total rooms is required.',
            'rooms.integer' => 'Rooms must be a number.',
            'capacity.required' => 'Capacity is required.',
            'capacity.integer' => 'Capacity must be a number.',
            'occupied.required' => 'Occupied beds is required.',
            'occupied.integer' => 'Occupied beds must be a number.',
            'occupied.lte' => 'Occupied beds cannot be greater than total capacity.',
            'status.in' => 'Status must be Active, Maintenance, or Inactive.',
            'notes.max' => 'Notes must not be greater than 1000 characters.',
        ];
    }
}