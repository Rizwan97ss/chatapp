<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateManagementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $managementId = $this->route('management')?->id;

        return [
            'member_no' => [
                'required',
                'string',
                'max:50',
                Rule::unique('management', 'member_no')->ignore($managementId),
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
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('management', 'email')->ignore($managementId),
            ],
            'phone' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+\-\s()]+$/'],
            'authority' => ['nullable', 'string', 'max:255', 'regex:/^[A-Za-z0-9\s.,&()\-\/]+$/'],
            'status' => ['required', Rule::in(['Active', 'Inactive'])],
            'responsibilities' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return (new StoreManagementRequest())->messages();
    }
}