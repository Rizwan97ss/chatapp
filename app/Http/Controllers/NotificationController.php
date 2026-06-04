<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNotificationRequest;
use App\Http\Requests\UpdateNotificationRequest;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Imports\NotificationImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Throwable;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'type', 'audience', 'status']);

        $notifications = Notification::query()
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('notification_no', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%")
                        ->orWhere('message', 'like', "%{$search}%")
                        ->orWhere('type', 'like', "%{$search}%")
                        ->orWhere('audience', 'like', "%{$search}%")
                        ->orWhere('channel', 'like', "%{$search}%");
                });
            })
            ->when($filters['type'] ?? null, fn ($query, $type) => $query->where('type', $type))
            ->when($filters['audience'] ?? null, fn ($query, $audience) => $query->where('audience', $audience))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->latest()
            ->paginate(10)
            ->through(fn ($notification) => [
                'id' => $notification->id,
                'notification_no' => $notification->notification_no,
                'title' => $notification->title,
                'message' => $notification->message,
                'type' => $notification->type,
                'audience' => $notification->audience,
                'channel' => $notification->channel,
                'date' => $notification->date,
                'status' => $notification->status,
            ])
            ->withQueryString();

        $total = Notification::count();

        return Inertia::render('Notifications/Index', [
            'notifications' => $notifications,
            'filters' => [
                'search' => $filters['search'] ?? '',
                'type' => $filters['type'] ?? '',
                'audience' => $filters['audience'] ?? '',
                'status' => $filters['status'] ?? '',
            ],
            'notificationStats' => [
                'overview' => [
                    'total' => $total,
                    'unread' => Notification::where('status', 'Unread')->count(),
                    'sent_today' => Notification::whereDate('created_at', today())->count(),
                    'failed' => Notification::where('status', 'Failed')->count(),
                    'delivered_percentage' => $total > 0
                        ? round((Notification::where('status', 'Sent')->count() / $total) * 100)
                        : 0,
                ],
                'deliveryBars' => Notification::query()
                    ->selectRaw('channel, COUNT(*) as total')
                    ->groupBy('channel')
                    ->get()
                    ->map(fn ($item) => [
                        'label' => $item->channel,
                        'value' => $total > 0 ? round(($item->total / $total) * 100) : 0,
                    ])
                    ->values(),
            ],
        ]);
    }

    public function store(StoreNotificationRequest $request)
    {
        try {
            Notification::create($request->validated());

            return back()->with('success', 'Notification created successfully.');
        } catch (Throwable $e) {
            Log::error('Notification create failed', ['message' => $e->getMessage()]);

            return back()->withInput()->withErrors([
                'server' => 'Something went wrong while saving notification.',
            ]);
        }
    }

    public function update(UpdateNotificationRequest $request, Notification $notification)
    {
        try {
            $notification->update($request->validated());

            return back()->with('success', 'Notification updated successfully.');
        } catch (Throwable $e) {
            Log::error('Notification update failed', [
                'notification_id' => $notification->id,
                'message' => $e->getMessage(),
            ]);

            return back()->withInput()->withErrors([
                'edit_server' => 'Something went wrong while updating notification.',
            ]);
        }
    }
public function import(Request $request)
{
    $request->validate([
        'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
    ]);

    try {
        $import = new NotificationImport();

        Excel::import($import, $request->file('file'));

        if ($import->failures()->isNotEmpty()) {
            return back()->withErrors([
                'import' => $import->failures()
                    ->take(10)
                    ->map(fn ($failure) => 'Row ' . $failure->row() . ': ' . implode(', ', $failure->errors()))
                    ->implode(' | '),
            ]);
        }

        return back()->with('success', 'Notifications imported successfully.');
    } catch (Throwable $e) {
        Log::error('Notification import failed', ['message' => $e->getMessage()]);

        return back()->withErrors([
            'import' => 'Import failed. Please check your Excel file and try again.',
        ]);
    }
}
    public function destroy(Notification $notification)
    {
        try {
            $notification->delete();

            return back()->with('success', 'Notification deleted successfully.');
        } catch (Throwable $e) {
            Log::error('Notification delete failed', [
                'notification_id' => $notification->id,
                'message' => $e->getMessage(),
            ]);

            return back()->with('error', 'Something went wrong while deleting notification.');
        }
    }
}