<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTeacherRequest;
use App\Http\Requests\UpdateTeacherRequest;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Throwable;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only([
            'search',
            'subject',
            'assigned_class',
            'status',
        ]);

        $teachers = Teacher::query()
            ->select([
                'id',
                'teacher_id',
                'full_name',
                'email',
                'phone',
                'gender',
                'date_of_birth',
                'subject',
                'assigned_class',
                'joining_date',
                'status',
                'address',
                'created_at',
            ])
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('full_name', 'like', "%{$search}%")
                        ->orWhere('teacher_id', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when($filters['subject'] ?? null, function ($query, $subject) {
                $query->where('subject', $subject);
            })
            ->when($filters['assigned_class'] ?? null, function ($query, $className) {
                $query->where('assigned_class', $className);
            })
            ->when($filters['status'] ?? null, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $teacherStats = [
            'cards' => [
                [
                    'label' => 'Total Teachers',
                    'value' => Teacher::count(),
                    'change' => 'All records',
                ],
                [
                    'label' => 'Active Staff',
                    'value' => Teacher::where('status', 'Active')->count(),
                    'change' => 'Currently active',
                ],
                [
                    'label' => 'On Leave',
                    'value' => Teacher::where('status', 'On Leave')->count(),
                    'change' => 'Temporarily away',
                ],
                [
                    'label' => 'Inactive',
                    'value' => Teacher::where('status', 'Inactive')->count(),
                    'change' => 'Inactive staff',
                ],
            ],
            'overview' => [
                'departments' => Teacher::whereNotNull('subject')
                    ->where('subject', '!=', '')
                    ->distinct()
                    ->count('subject'),

                'active' => Teacher::where('status', 'Active')->count(),
                'on_leave' => Teacher::where('status', 'On Leave')->count(),
            ],
        ];
        // dd($teachers,$teacherStats);

        return Inertia::render('Teachers/Index', [
            'teachers' => $teachers,
            'filters' => [
                'search' => $filters['search'] ?? '',
                'subject' => $filters['subject'] ?? '',
                'assigned_class' => $filters['assigned_class'] ?? '',
                'status' => $filters['status'] ?? '',
            ],
            'teacherStats' => $teacherStats,
            'subjects' => Teacher::query()
                ->whereNotNull('subject')
                ->where('subject', '!=', '')
                ->distinct()
                ->orderBy('subject')
                ->pluck('subject'),

            'assignedClasses' => Teacher::query()
                ->whereNotNull('assigned_class')
                ->where('assigned_class', '!=', '')
                ->distinct()
                ->orderBy('assigned_class')
                ->pluck('assigned_class'),
        ]);
    }

    public function store(StoreTeacherRequest $request)
    {
        try {
            Teacher::create($request->validated());

            return back()->with('success', 'Teacher created successfully.');
        } catch (Throwable $e) {
            Log::error('Teacher create failed', [
                'message' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors([
                    'server' => 'Something went wrong while saving teacher.',
                ]);
        }
    }

    public function update(UpdateTeacherRequest $request, Teacher $teacher)
    {
        try {
            $teacher->update($request->validated());

            return back()->with('success', 'Teacher updated successfully.');
        } catch (Throwable $e) {
            Log::error('Teacher update failed', [
                'teacher_id' => $teacher->id,
                'message' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors([
                    'edit_server' => 'Something went wrong while updating teacher.',
                ]);
        }
    }

    public function destroy(Teacher $teacher)
    {
        try {
            $teacher->delete();

            return back()->with('success', 'Teacher deleted successfully.');
        } catch (Throwable $e) {
            Log::error('Teacher delete failed', [
                'teacher_id' => $teacher->id,
                'message' => $e->getMessage(),
            ]);

            return back()->with('error', 'Something went wrong while deleting teacher.');
        }
    }
}
