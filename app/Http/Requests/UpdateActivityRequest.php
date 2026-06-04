<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $activityId = $this->route('activity')?->id;

        return [
            'activity_no' => [
                'required',
                'string',
                'max:50',
                Rule::unique('activities', 'activity_no')->ignore($activityId),
                'regex:/^[A-Za-z0-9\-\/_]+$/',
            ],

            'title' => [
                'required',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9\s.,\'()\-]+$/',
            ],

            'type' => [
                'nullable',
                'string',
                'max:100',
                'regex:/^[A-Za-z\s]+$/',
            ],

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

            'venue' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9\s.,#\-\/]+$/',
            ],

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