<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStaffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'staff_no' => [
                'required',
                'string',
                'max:50',
                'unique:staff,staff_no',
                'regex:/^[A-Za-z0-9\-\/_]+$/',
            ],

            'full_name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[A-Za-z\s.\'-]+$/',
            ],

            'role' => [
                'required',
                'string',
                'max:100',
                'regex:/^[A-Za-z\s\/\-]+$/',
            ],

            'department' => [
                'required',
                'string',
                'max:100',
                'regex:/^[A-Za-z\s\/\-]+$/',
            ],

            'phone' => [
                'required',
                'string',
                'max:30',
                'regex:/^[0-9+\s\-()]+$/',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
                'unique:staff,email',
            ],

            'shift' => [
                'required',
                Rule::in(['Morning', 'Evening', 'Night']),
            ],

            'salary' => [
                'required',
                'numeric',
                'min:0',
            ],

            'joining_date' => [
                'nullable',
                'date',
            ],

            'status' => [
                'required',
                Rule::in(['Active', 'On Leave', 'Inactive']),
            ],

            'address' => [
                'nullable',
                'string',
                'max:1000',
                'regex:/^[A-Za-z0-9\s.,#;:()\-\/\r\n]+$/',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'staff_no.required' => 'Staff ID is required.',
            'staff_no.unique' => 'Staff ID already exists.',
            'staff_no.regex' => 'Staff ID may only contain letters, numbers, hyphen, slash, and underscore.',

            'full_name.required' => 'Full name is required.',
            'full_name.regex' => 'Full name may only contain letters, spaces, dot, apostrophe, and hyphen.',

            'role.required' => 'Role is required.',
            'role.regex' => 'Role may only contain letters, spaces, slash, and hyphen.',

            'department.required' => 'Department is required.',
            'department.regex' => 'Department may only contain letters, spaces, slash, and hyphen.',

            'phone.required' => 'Phone number is required.',
            'phone.regex' => 'Phone number may only contain numbers, +, spaces, hyphen, and brackets.',

            'email.email' => 'Email must be valid.',
            'email.unique' => 'Email already exists.',

            'shift.in' => 'Shift must be Morning, Evening, or Night.',

            'salary.required' => 'Salary is required.',
            'salary.numeric' => 'Salary must be a valid number.',
            'salary.min' => 'Salary cannot be negative.',

            'status.in' => 'Status must be Active, On Leave, or Inactive.',

            'address.regex' => 'Address contains invalid special characters.',
        ];
    }
}