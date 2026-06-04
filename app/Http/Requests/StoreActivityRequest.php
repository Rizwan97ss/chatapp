<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Allowed: ACT-1001, ACT_1001, ACT/2026/001
            'activity_no' => [
                'required',
                'string',
                'max:50',
                'unique:activities,activity_no',
                'regex:/^[A-Za-z0-9\-\/_]+$/',
            ],

            // Allowed: letters, numbers, spaces, comma, dot, hyphen, apostrophe, brackets
            'title' => [
                'required',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9\s.,\'()\-]+$/',
            ],

            // Allowed: letters and spaces only
            'type' => [
                'nullable',
                'string',
                'max:100',
                'regex:/^[A-Za-z\s]+$/',
            ],

            // Allowed: Grade 8, Grade 8 - 11, All Classes
            'class_name' => [
                'nullable',
                'string',
                'max:100',
                'regex:/^[A-Za-z0-9\s\-]+$/',
            ],

            'activity_date' => [
                'nullable',
                'date',
            ],

            'activity_time' => [
                'nullable',
                'date_format:H:i',
            ],

            // Allowed: Main Ground, Room A-12, Hall #2
            'venue' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9\s.,#\-\/]+$/',
            ],

            // Allowed: Sports Department, Mr. Ali, Science Club
            'organizer' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9\s.,\'\-]+$/',
            ],

            'status' => [
                'required',
                Rule::in(['Upcoming', 'Completed', 'Cancelled']),
            ],

            // Allows normal punctuation for sentences
            'description' => [
                'nullable',
                'string',
                'max:1000',
                'regex:/^[A-Za-z0-9\s.,;:!?\'"()\-\/\r\n]+$/',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'activity_no.required' => 'Activity ID is required.',
            'activity_no.unique' => 'Activity ID already exists.',
            'activity_no.regex' => 'Activity ID may only contain letters, numbers, hyphen, slash, and underscore.',

            'title.required' => 'Activity title is required.',
            'title.regex' => 'Title may only contain letters, numbers, spaces, comma, dot, hyphen, apostrophe, and brackets.',

            'type.regex' => 'Type may only contain letters and spaces.',

            'class_name.regex' => 'Class may only contain letters, numbers, spaces, and hyphen.',

            'activity_time.date_format' => 'Time must be in valid HH:MM format.',

            'venue.regex' => 'Venue may only contain letters, numbers, spaces, comma, dot, #, hyphen, and slash.',

            'organizer.regex' => 'Organizer may only contain letters, numbers, spaces, comma, dot, apostrophe, and hyphen.',

            'status.required' => 'Status is required.',
            'status.in' => 'Status must be Upcoming, Completed, or Cancelled.',

            'description.regex' => 'Description contains invalid special characters.',
        ];
    }
}