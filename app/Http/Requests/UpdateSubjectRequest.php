<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $subjectId = $this->route('subject')?->id;

        return [
            'subject_id' => [
                'required',
                'string',
                'max:50',
                Rule::unique('subjects', 'subject_id')->ignore($subjectId),
            ],
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('subjects', 'code')->ignore($subjectId),
            ],
            'class_name' => ['required', 'string', 'max:100'],
            'teacher_name' => ['nullable', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:Core,Elective,Optional'],
            'weekly_hours' => ['required', 'integer', 'min:0', 'max:100'],
            'credit' => ['nullable', 'integer', 'min:0', 'max:1000'],
            'status' => ['required', 'string', 'in:Active,Inactive'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }
}