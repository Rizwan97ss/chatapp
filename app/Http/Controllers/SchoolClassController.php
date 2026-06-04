<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSchoolClassRequest;
use App\Http\Requests\UpdateSchoolClassRequest;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Throwable;

class SchoolClassController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only([
            'search',
            'name',
            'status',
        ]);

        $classes = SchoolClass::query()
            ->select([
                'id',
                'class_id',
                'name',
                'section',
                'teacher_name',
                'room',
                'capacity',
                'students_count',
                'status',
                'description',
                'created_at',
            ])
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('class_id', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('section', 'like', "%{$search}%")
                        ->orWhere('teacher_name', 'like', "%{$search}%")
                        ->orWhere('room', 'like', "%{$search}%");
                });
            })
            ->when($filters['name'] ?? null, function ($query, $name) {
                $query->where('name', $name);
            })
            ->when($filters['status'] ?? null, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $classStats = [
            'cards' => [
                [
                    'label' => 'Total Classes',
                    'value' => SchoolClass::count(),
                    'change' => 'All class sections',
                ],
                [
                    'label' => 'Sections',
                    'value' => SchoolClass::distinct('section')->count('section'),
                    'change' => 'Unique sections',
                ],
                [
                    'label' => 'Assigned Teachers',
                    'value' => SchoolClass::whereNotNull('teacher_name')->where('teacher_name', '!=', '')->count(),
                    'change' => 'With class teacher',
                ],
                [
                    'label' => 'Total Students',
                    'value' => SchoolClass::sum('students_count'),
                    'change' => 'Current enrolled count',
                ],
            ],
            'overview' => [
                'active' => SchoolClass::where('status', 'Active')->count(),
                'inactive' => SchoolClass::where('status', 'Inactive')->count(),
                'capacity' => SchoolClass::sum('capacity'),
                'students' => SchoolClass::sum('students_count'),
            ],
        ];

        return Inertia::render('Classes/Index', [
            'classes' => $classes,
            'filters' => [
                'search' => $filters['search'] ?? '',
                'name' => $filters['name'] ?? '',
                'status' => $filters['status'] ?? '',
            ],
            'classStats' => $classStats,
            'classNames' => SchoolClass::query()
                ->whereNotNull('name')
                ->where('name', '!=', '')
                ->distinct()
                ->orderBy('name')
                ->pluck('name')
                ->values()
                ->toArray(),
        ]);
    }

    public function store(StoreSchoolClassRequest $request)
    {
        try {
            SchoolClass::create($request->validated());

            return back()->with('success', 'Class created successfully.');
        } catch (Throwable $e) {
            Log::error('Class create failed', [
                'message' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors([
                    'server' => 'Something went wrong while saving class.',
                ]);
        }
    }

    public function update(UpdateSchoolClassRequest $request, SchoolClass $schoolClass)
    {
        try {
            $schoolClass->update($request->validated());

            return back()->with('success', 'Class updated successfully.');
        } catch (Throwable $e) {
            Log::error('Class update failed', [
                'class_id' => $schoolClass->id,
                'message' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors([
                    'edit_server' => 'Something went wrong while updating class.',
                ]);
        }
    }

    public function destroy(SchoolClass $schoolClass)
    {
        try {
            $schoolClass->delete();

            return back()->with('success', 'Class deleted successfully.');
        } catch (Throwable $e) {
            Log::error('Class delete failed', [
                'class_id' => $schoolClass->id,
                'message' => $e->getMessage(),
            ]);

            return back()->with('error', 'Something went wrong while deleting class.');
        }
    }
}