<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreActivityRequest;
use App\Http\Requests\UpdateActivityRequest;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Throwable;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only([
            'search',
            'type',
            'class_name',
            'status',
        ]);

        $activities = Activity::query()
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('activity_no', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%")
                        ->orWhere('type', 'like', "%{$search}%")
                        ->orWhere('class_name', 'like', "%{$search}%")
                        ->orWhere('venue', 'like', "%{$search}%")
                        ->orWhere('organizer', 'like', "%{$search}%");
                });
            })
            ->when($filters['type'] ?? null, fn ($query, $type) => $query->where('type', $type))
            ->when($filters['class_name'] ?? null, fn ($query, $className) => $query->where('class_name', $className))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->latest()
            ->paginate(10)
            ->through(fn ($activity) => [
                'id' => $activity->id,
                'activity_no' => $activity->activity_no,
                'title' => $activity->title,
                'type' => $activity->type,
                'class_name' => $activity->class_name,
                'activity_date' => optional($activity->activity_date)->format('Y-m-d'),
                'activity_time' => $activity->activity_time,
                'venue' => $activity->venue,
                'organizer' => $activity->organizer,
                'status' => $activity->status,
                'description' => $activity->description,
            ])
            ->withQueryString();

        $activityStats = [
            'cards' => [
                [
                    'label' => 'Total Activities',
                    'value' => Activity::count(),
                    'change' => 'All activity records',
                ],
                [
                    'label' => 'Upcoming Events',
                    'value' => Activity::where('status', 'Upcoming')->count(),
                    'change' => 'Planned events',
                ],
                [
                    'label' => 'Completed Events',
                    'value' => Activity::where('status', 'Completed')->count(),
                    'change' => 'Finished events',
                ],
                [
                    'label' => 'Cancelled Events',
                    'value' => Activity::where('status', 'Cancelled')->count(),
                    'change' => 'Cancelled events',
                ],
            ],
            'overview' => [
                'total' => Activity::count(),
                'upcoming' => Activity::where('status', 'Upcoming')->count(),
                'completed' => Activity::where('status', 'Completed')->count(),
                'cancelled' => Activity::where('status', 'Cancelled')->count(),
            ],
            'participationBars' => Activity::query()
                ->selectRaw('type, COUNT(*) as total')
                ->whereNotNull('type')
                ->groupBy('type')
                ->get()
                ->map(function ($item) {
                    $total = Activity::count();

                    return [
                        'label' => $item->type,
                        'value' => $total > 0 ? round(($item->total / $total) * 100) : 0,
                    ];
                })
                ->values(),
        ];

        return Inertia::render('Activities/Index', [
            'activities' => $activities,
            'filters' => [
                'search' => $filters['search'] ?? '',
                'type' => $filters['type'] ?? '',
                'class_name' => $filters['class_name'] ?? '',
                'status' => $filters['status'] ?? '',
            ],
            'activityStats' => $activityStats,
            'types' => Activity::query()
                ->whereNotNull('type')
                ->where('type', '!=', '')
                ->distinct()
                ->orderBy('type')
                ->pluck('type')
                ->values()
                ->toArray(),
            'classNames' => Activity::query()
                ->whereNotNull('class_name')
                ->where('class_name', '!=', '')
                ->distinct()
                ->orderBy('class_name')
                ->pluck('class_name')
                ->values()
                ->toArray(),
        ]);
    }

public function store(StoreActivityRequest $request)
{
    try {
        Activity::create($request->validated());

        return back()->with('success', 'Activity created successfully.');
    } catch (Throwable $e) {
        Log::error('Activity create failed', [
            'message' => $e->getMessage(),
        ]);

        return back()
            ->withInput()
            ->withErrors([
                'server' => 'Something went wrong while saving activity.',
            ]);
    }
}

public function update(UpdateActivityRequest $request, Activity $activity)
{
    try {
        $activity->update($request->validated());

        return back()->with('success', 'Activity updated successfully.');
    } catch (Throwable $e) {
        Log::error('Activity update failed', [
            'activity_id' => $activity->id,
            'message' => $e->getMessage(),
        ]);

        return back()
            ->withInput()
            ->withErrors([
                'edit_server' => 'Something went wrong while updating activity.',
            ]);
    }
}
    public function destroy(Activity $activity)
    {
        try {
            $activity->delete();

            return back()->with('success', 'Activity deleted successfully.');
        } catch (Throwable $e) {
            Log::error('Activity delete failed', [
                'activity_id' => $activity->id,
                'message' => $e->getMessage(),
            ]);

            return back()->with('error', 'Something went wrong while deleting activity.');
        }
    }
}