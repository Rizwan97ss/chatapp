<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $notificationId = $this->route('notification')?->id;

        return [
            'notification_no' => [
                'required',
                'string',
                'max:50',
                Rule::unique('notifications', 'notification_no')->ignore($notificationId),
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
        return (new StoreNotificationRequest())->messages();
    }
}