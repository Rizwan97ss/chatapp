<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubjectRequest;
use App\Http\Requests\UpdateSubjectRequest;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Throwable;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only([
            'search',
            'class_name',
            'type',
            'status',
        ]);

        $subjects = Subject::query()
            ->select([
                'id',
                'subject_id',
                'name',
                'code',
                'class_name',
                'teacher_name',
                'type',
                'weekly_hours',
                'credit',
                'status',
                'description',
                'created_at',
            ])
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('subject_id', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('class_name', 'like', "%{$search}%")
                        ->orWhere('teacher_name', 'like', "%{$search}%");
                });
            })
            ->when($filters['class_name'] ?? null, function ($query, $className) {
                $query->where('class_name', $className);
            })
            ->when($filters['type'] ?? null, function ($query, $type) {
                $query->where('type', $type);
            })
            ->when($filters['status'] ?? null, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $subjectStats = [
            'cards' => [
                [
                    'label' => 'Total Subjects',
                    'value' => Subject::count(),
                    'change' => 'All subjects',
                ],
                [
                    'label' => 'Core Subjects',
                    'value' => Subject::where('type', 'Core')->count(),
                    'change' => 'Core curriculum',
                ],
                [
                    'label' => 'Electives',
                    'value' => Subject::where('type', 'Elective')->count(),
                    'change' => 'Elective subjects',
                ],
                [
                    'label' => 'Assigned Teachers',
                    'value' => Subject::whereNotNull('teacher_name')
                        ->where('teacher_name', '!=', '')
                        ->count(),
                    'change' => 'Teacher assigned',
                ],
            ],
            'overview' => [
                'weekly_hours' => Subject::sum('weekly_hours'),
                'active' => Subject::where('status', 'Active')->count(),
                'inactive' => Subject::where('status', 'Inactive')->count(),
                'pending_assignment' => Subject::whereNull('teacher_name')
                    ->orWhere('teacher_name', '')
                    ->count(),
                'core' => Subject::where('type', 'Core')->count(),
                'elective' => Subject::where('type', 'Elective')->count(),
                'optional' => Subject::where('type', 'Optional')->count(),
            ],
        ];

        return Inertia::render('Subjects/Index', [
            'subjects' => $subjects,
            'filters' => [
                'search' => $filters['search'] ?? '',
                'class_name' => $filters['class_name'] ?? '',
                'type' => $filters['type'] ?? '',
                'status' => $filters['status'] ?? '',
            ],
            'subjectStats' => $subjectStats,
            'classNames' => Subject::query()
                ->whereNotNull('class_name')
                ->where('class_name', '!=', '')
                ->distinct()
                ->orderBy('class_name')
                ->pluck('class_name')
                ->values()
                ->toArray(),
            'teacherNames' => Subject::query()
                ->whereNotNull('teacher_name')
                ->where('teacher_name', '!=', '')
                ->distinct()
                ->orderBy('teacher_name')
                ->pluck('teacher_name')
                ->values()
                ->toArray(),
        ]);
    }

    public function store(StoreSubjectRequest $request)
    {
        try {
            Subject::create($request->validated());

            return back()->with('success', 'Subject created successfully.');
        } catch (Throwable $e) {
            Log::error('Subject create failed', [
                'message' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors([
                    'server' => 'Something went wrong while saving subject.',
                ]);
        }
    }

    public function update(UpdateSubjectRequest $request, Subject $subject)
    {
        try {
            $subject->update($request->validated());

            return back()->with('success', 'Subject updated successfully.');
        } catch (Throwable $e) {
            Log::error('Subject update failed', [
                'subject_id' => $subject->id,
                'message' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors([
                    'edit_server' => 'Something went wrong while updating subject.',
                ]);
        }
    }

    public function destroy(Subject $subject)
    {
        try {
            $subject->delete();

            return back()->with('success', 'Subject deleted successfully.');
        } catch (Throwable $e) {
            Log::error('Subject delete failed', [
                'subject_id' => $subject->id,
                'message' => $e->getMessage(),
            ]);

            return back()->with('error', 'Something went wrong while deleting subject.');
        }
    }
}