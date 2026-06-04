<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateParentProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $parentId = $this->route('parentProfile')?->id;

        return [
            'parent_no' => [
                'required',
                'string',
                'max:50',
                Rule::unique('parent_profiles', 'parent_no')->ignore($parentId),
                'regex:/^[A-Za-z0-9\-\/_]+$/',
            ],
            'full_name' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z\s.\'-]+$/'],
            'relation' => ['required', Rule::in(['Father', 'Mother', 'Guardian'])],
            'student_name' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z\s.\'-]+$/'],
            'class_name' => ['required', 'string', 'max:100', 'regex:/^[A-Za-z0-9\s\-]+$/'],
            'phone' => ['required', 'string', 'max:30', 'regex:/^[0-9+\s\-()]+$/'],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('parent_profiles', 'email')->ignore($parentId),
            ],
            'occupation' => ['nullable', 'string', 'max:255', 'regex:/^[A-Za-z\s.\'-]+$/'],
            'status' => ['required', Rule::in(['Active', 'Pending', 'Inactive'])],
            'address' => ['nullable', 'string', 'max:1000', 'regex:/^[A-Za-z0-9\s.,#;:()\-\/\r\n]+$/'],
        ];
    }

    public function messages(): array
    {
        return (new StoreParentProfileRequest())->messages();
    }
}