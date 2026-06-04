<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTeacherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $teacherId = $this->route('teacher')?->id;

        return [
            'teacher_id' => [
                'required',
                'string',
                'max:50',
                Rule::unique('teachers', 'teacher_id')->ignore($teacherId),
            ],
            'full_name' => ['required', 'string', 'max:255'],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('teachers', 'email')->ignore($teacherId),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
            'gender' => ['nullable', 'string', 'in:Male,Female'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'subject' => ['required', 'string', 'max:255'],
            'assigned_class' => ['nullable', 'string', 'max:100'],
            'joining_date' => ['nullable', 'date'],
            'status' => ['required', 'string', 'in:Active,On Leave,Inactive'],
            'address' => ['nullable', 'string', 'max:1000'],
        ];
    }
}