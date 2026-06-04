<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreManagementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'member_no' => [
                'required',
                'string',
                'max:50',
                'unique:management,member_no',
                'regex:/^[A-Za-z0-9\-\/_]+$/',
            ],
            'name' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z\s.\'-]+$/'],
            'role' => ['required', Rule::in([
                'Principal',
                'Vice Principal',
                'Academic Director',
                'Finance Director',
                'HR & Admin Head',
                'Board Member',
            ])],
            'department' => ['required', Rule::in([
                'Principal Office',
                'Academic Affairs',
                'Finance',
                'Human Resources',
                'Board',
                'Administration',
            ])],
            'email' => ['required', 'email', 'max:255', 'unique:management,email'],
            'phone' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+\-\s()]+$/'],
            'authority' => ['nullable', 'string', 'max:255', 'regex:/^[A-Za-z0-9\s.,&()\-\/]+$/'],
            'status' => ['required', Rule::in(['Active', 'Inactive'])],
            'responsibilities' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'member_no.required' => 'Member ID is required.',
            'member_no.unique' => 'Member ID already exists.',
            'member_no.regex' => 'Member ID may only contain letters, numbers, hyphen, slash, and underscore.',

            'name.required' => 'Full name is required.',
            'name.regex' => 'Full name contains invalid characters.',

            'role.required' => 'Role is required.',
            'role.in' => 'Invalid management role selected.',

            'department.required' => 'Department is required.',
            'department.in' => 'Invalid department selected.',

            'email.required' => 'Email is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'Email already exists.',

            'phone.regex' => 'Phone may only contain numbers, spaces, plus sign, hyphen, and brackets.',

            'authority.regex' => 'Authority contains invalid characters.',

            'status.required' => 'Status is required.',
            'status.in' => 'Status must be Active or Inactive.',

            'responsibilities.max' => 'Responsibilities must not be greater than 1000 characters.',
        ];
    }
}