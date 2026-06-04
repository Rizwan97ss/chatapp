<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStaffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $staffId = $this->route('staff')?->id;

        return [
            'staff_no' => [
                'required',
                'string',
                'max:50',
                Rule::unique('staff', 'staff_no')->ignore($staffId),
                'regex:/^[A-Za-z0-9\-\/_]+$/',
            ],

            'full_name' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z\s.\'-]+$/'],
            'role' => ['required', 'string', 'max:100', 'regex:/^[A-Za-z\s\/\-]+$/'],
            'department' => ['required', 'string', 'max:100', 'regex:/^[A-Za-z\s\/\-]+$/'],
            'phone' => ['required', 'string', 'max:30', 'regex:/^[0-9+\s\-()]+$/'],

            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('staff', 'email')->ignore($staffId),
            ],

            'shift' => ['required', Rule::in(['Morning', 'Evening', 'Night'])],
            'salary' => ['required', 'numeric', 'min:0'],
            'joining_date' => ['nullable', 'date'],
            'status' => ['required', Rule::in(['Active', 'On Leave', 'Inactive'])],
            'address' => ['nullable', 'string', 'max:1000', 'regex:/^[A-Za-z0-9\s.,#;:()\-\/\r\n]+$/'],
        ];
    }

    public function messages(): array
    {
        return (new StoreStaffRequest())->messages();
    }
}