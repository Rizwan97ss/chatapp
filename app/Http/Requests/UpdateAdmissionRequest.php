<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAdmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $admissionId = $this->route('admission')->id;

        return [
            // 'application_no' => [
            //     'required',
            //     'string',
            //     'max:50',
            //     Rule::unique('admissions', 'application_no')->ignore($admissionId),
            // ],
            'student_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],

            'gender' => ['nullable', 'in:Male,Female'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],

            'applied_class' => ['required', 'string', 'max:100'],
            'previous_school' => ['nullable', 'string', 'max:255'],

            'parent_name' => ['required', 'string', 'max:255'],
            'parent_phone' => ['required', 'string', 'max:30'],
            'parent_email' => ['nullable', 'email', 'max:255'],

            'application_date' => ['nullable', 'date'],
            'status' => ['required', 'in:New,Under Review,Approved,Rejected,Waitlisted'],

            'address' => ['nullable', 'string', 'max:1000'],
            'remarks' => ['nullable', 'string', 'max:1000'],
        ];
    }
}