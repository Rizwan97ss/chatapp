<?php

namespace App\Imports;

use App\Models\Notification;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class NotificationImport implements
    ToModel,
    WithHeadingRow,
    WithValidation,
    WithChunkReading,
    WithBatchInserts,
    SkipsOnFailure
{
    use SkipsFailures;

    public function model(array $row)
    {
        return new Notification([
            'notification_no' => trim($row['notification_no'] ?? ''),
            'title' => trim($row['title'] ?? ''),
            'message' => trim($row['message'] ?? ''),
            'type' => trim($row['type'] ?? ''),
            'audience' => trim($row['audience'] ?? ''),
            'channel' => trim($row['channel'] ?? ''),
            'date' => $row['date'] ?? null,
            'status' => trim($row['status'] ?? 'Scheduled'),
        ]);
    }

    public function rules(): array
    {
        return [
            '*.notification_no' => ['required', 'string', 'max:50', Rule::unique('notifications', 'notification_no')],
            '*.title' => ['required', 'string', 'max:255'],
            '*.message' => ['required', 'string', 'max:1000'],
            '*.type' => ['required', Rule::in(['General', 'Fee', 'Exam', 'Meeting', 'Transport', 'Attendance'])],
            '*.audience' => ['required', Rule::in(['Students', 'Parents', 'Teachers', 'Staffs', 'All Users'])],
            '*.channel' => ['required', Rule::in(['App', 'Email', 'SMS', 'Email / SMS', 'All Channels'])],
            '*.date' => ['required', 'date'],
            '*.status' => ['required', Rule::in(['Sent', 'Scheduled', 'Unread', 'Failed'])],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            '*.notification_no.required' => 'Notification ID is required.',
            '*.notification_no.unique' => 'Notification ID already exists.',
            '*.title.required' => 'Notification title is required.',
            '*.message.required' => 'Notification message is required.',
            '*.type.required' => 'Notification type is required.',
            '*.type.in' => 'Invalid notification type selected.',
            '*.audience.required' => 'Audience is required.',
            '*.audience.in' => 'Invalid audience selected.',
            '*.channel.required' => 'Notification channel is required.',
            '*.channel.in' => 'Invalid notification channel selected.',
            '*.date.required' => 'Notification date is required.',
            '*.date.date' => 'Notification date must be valid.',
            '*.status.required' => 'Status is required.',
            '*.status.in' => 'Status must be Sent, Scheduled, Unread, or Failed.',
        ];
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function batchSize(): int
    {
        return 500;
    }
}