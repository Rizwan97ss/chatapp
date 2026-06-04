<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateHostelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $hostelId = $this->route('hostel')?->id;

        return [
            'hostel_no' => [
                'required',
                'string',
                'max:50',
                Rule::unique('hostels', 'hostel_no')->ignore($hostelId),
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
        return (new StoreHostelRequest())->messages();
    }
}