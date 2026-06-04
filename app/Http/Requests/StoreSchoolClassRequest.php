<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSchoolClassRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'class_id' => ['required', 'string', 'max:50', 'unique:school_classes,class_id'],
            'name' => ['required', 'string', 'max:100'],
            'section' => ['required', 'string', 'max:20'],
            'teacher_name' => ['nullable', 'string', 'max:255'],
            'room' => ['nullable', 'string', 'max:100'],
            'capacity' => ['nullable', 'integer', 'min:1', 'max:500'],
            'students_count' => ['nullable', 'integer', 'min:0', 'max:500'],
            'status' => ['required', 'string', 'in:Active,Inactive'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }
}