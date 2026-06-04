<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreParentProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'parent_no' => [
                'required',
                'string',
                'max:50',
                'unique:parent_profiles,parent_no',
                'regex:/^[A-Za-z0-9\-\/_]+$/',
            ],
            'full_name' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z\s.\'-]+$/'],
            'relation' => ['required', Rule::in(['Father', 'Mother', 'Guardian'])],
            'student_name' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z\s.\'-]+$/'],
            'class_name' => ['required', 'string', 'max:100', 'regex:/^[A-Za-z0-9\s\-]+$/'],
            'phone' => ['required', 'string', 'max:30', 'regex:/^[0-9+\s\-()]+$/'],
            'email' => ['nullable', 'email', 'max:255', 'unique:parent_profiles,email'],
            'occupation' => ['nullable', 'string', 'max:255', 'regex:/^[A-Za-z\s.\'-]+$/'],
            'status' => ['required', Rule::in(['Active', 'Pending', 'Inactive'])],
            'address' => ['nullable', 'string', 'max:1000', 'regex:/^[A-Za-z0-9\s.,#;:()\-\/\r\n]+$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'parent_no.required' => 'Parent ID is required.',
            'parent_no.unique' => 'Parent ID already exists.',
            'parent_no.regex' => 'Parent ID may only contain letters, numbers, hyphen, slash, and underscore.',

            'full_name.required' => 'Full name is required.',
            'full_name.regex' => 'Full name may only contain letters, spaces, dot, apostrophe, and hyphen.',

            'relation.required' => 'Relation is required.',
            'relation.in' => 'Relation must be Father, Mother, or Guardian.',

            'student_name.required' => 'Student name is required.',
            'student_name.regex' => 'Student name contains invalid characters.',

            'class_name.required' => 'Class is required.',
            'class_name.regex' => 'Class may only contain letters, numbers, spaces, and hyphen.',

            'phone.required' => 'Phone number is required.',
            'phone.regex' => 'Phone number may only contain numbers, +, spaces, hyphen, and brackets.',

            'email.email' => 'Email must be valid.',
            'email.unique' => 'Email already exists.',

            'occupation.regex' => 'Occupation may only contain letters, spaces, dot, apostrophe, and hyphen.',

            'status.required' => 'Status is required.',
            'status.in' => 'Status must be Active, Pending, or Inactive.',

            'address.regex' => 'Address contains invalid special characters.',
        ];
    }
}