<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'notification_no' => [
                'required',
                'string',
                'max:50',
                'unique:notifications,notification_no',
                'regex:/^[A-Za-z0-9\-\/_]+$/',
            ],
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:1000'],
            'type' => ['required', Rule::in([
                'General',
                'Fee',
                'Exam',
                'Meeting',
                'Transport',
                'Attendance',
            ])],
            'audience' => ['required', Rule::in([
                'Students',
                'Parents',
                'Teachers',
                'Staffs',
                'All Users',
            ])],
            'channel' => ['required', Rule::in([
                'App',
                'Email',
                'SMS',
                'Email / SMS',
                'All Channels',
            ])],
            'date' => ['required', 'date'],
            'status' => ['required', Rule::in([
                'Sent',
                'Scheduled',
                'Unread',
                'Failed',
            ])],
        ];
    }

    public function messages(): array
    {
        return [
            'notification_no.required' => 'Notification ID is required.',
            'notification_no.unique' => 'Notification ID already exists.',
            'notification_no.regex' => 'Notification ID may only contain letters, numbers, hyphen, slash, and underscore.',
            'notification_no.max' => 'Notification ID must not be greater than 50 characters.',

            'title.required' => 'Notification title is required.',
            'title.max' => 'Notification title must not be greater than 255 characters.',

            'message.required' => 'Notification message is required.',
            'message.max' => 'Notification message must not be greater than 1000 characters.',

            'type.required' => 'Notification type is required.',
            'type.in' => 'Invalid notification type selected.',

            'audience.required' => 'Audience is required.',
            'audience.in' => 'Invalid audience selected.',

            'channel.required' => 'Notification channel is required.',
            'channel.in' => 'Invalid notification channel selected.',

            'date.required' => 'Notification date is required.',
            'date.date' => 'Please enter a valid notification date.',

            'status.required' => 'Notification status is required.',
            'status.in' => 'Status must be Sent, Scheduled, Unread, or Failed.',
        ];
    }
}