<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
{
   public function authorize(): bool
{
    return true;
}

public function rules(): array
{
    return [
        'admission_no' => ['required', 'string', 'max:50', 'unique:students,admission_no'],
        'full_name' => ['required', 'string', 'max:255'],

        'email' => ['nullable', 'email', 'max:255', 'unique:students,email'],
        'phone' => ['nullable', 'string', 'max:30'],

        'gender' => ['nullable', 'in:Male,Female'],
        'date_of_birth' => ['nullable', 'date', 'before:today'],

        'class_name' => ['required', 'string', 'max:100'],
        'section' => ['nullable', 'string', 'max:20'],

        'parent_name' => ['required', 'string', 'max:255'],
        'parent_phone' => ['required', 'string', 'max:30'],

        'admission_date' => ['nullable', 'date'],
        'status' => ['required', 'in:Active,Pending,Inactive'],

        'address' => ['nullable', 'string', 'max:1000'],
    ];
}
}
