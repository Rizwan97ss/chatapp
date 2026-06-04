<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreParentProfileRequest;
use App\Http\Requests\UpdateParentProfileRequest;
use App\Imports\ParentProfilesImport;
use App\Models\ParentProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class ParentProfileController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only([
            'search',
            'relation',
            'class_name',
            'status',
        ]);

        $parents = ParentProfile::query()
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('parent_no', 'like', "%{$search}%")
                        ->orWhere('full_name', 'like', "%{$search}%")
                        ->orWhere('student_name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('occupation', 'like', "%{$search}%");
                });
            })
            ->when($filters['relation'] ?? null, fn ($query, $relation) => $query->where('relation', $relation))
            ->when($filters['class_name'] ?? null, fn ($query, $className) => $query->where('class_name', $className))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->latest()
            ->paginate(10)
            ->through(fn ($parent) => [
                'id' => $parent->id,
                'parent_no' => $parent->parent_no,
                'full_name' => $parent->full_name,
                'relation' => $parent->relation,
                'student_name' => $parent->student_name,
                'class_name' => $parent->class_name,
                'phone' => $parent->phone,
                'email' => $parent->email,
                'occupation' => $parent->occupation,
                'status' => $parent->status,
                'address' => $parent->address,
            ])
            ->withQueryString();

        $totalParents = ParentProfile::count();
        $activeParents = ParentProfile::where('status', 'Active')->count();

        $parentStats = [
            'cards' => [
                [
                    'label' => 'Total Parents',
                    'value' => $totalParents,
                    'change' => 'All parent records',
                ],
                [
                    'label' => 'Active Parents',
                    'value' => $activeParents,
                    'change' => 'Active profiles',
                ],
                [
                    'label' => 'Linked Students',
                    'value' => ParentProfile::distinct('student_name')->count('student_name'),
                    'change' => 'Unique linked students',
                ],
                [
                    'label' => 'Pending Profiles',
                    'value' => ParentProfile::where('status', 'Pending')->count(),
                    'change' => 'Need verification',
                ],
            ],
            'overview' => [
                'total' => $totalParents,
                'active' => $activeParents,
                'pending' => ParentProfile::where('status', 'Pending')->count(),
                'inactive' => ParentProfile::where('status', 'Inactive')->count(),
                'active_percentage' => $totalParents > 0 ? round(($activeParents / $totalParents) * 100) : 0,
            ],
            'engagementBars' => ParentProfile::query()
                ->selectRaw('class_name, COUNT(*) as total')
                ->whereNotNull('class_name')
                ->groupBy('class_name')
                ->get()
                ->map(function ($item) use ($totalParents) {
                    return [
                        'label' => $item->class_name,
                        'value' => $totalParents > 0 ? round(($item->total / $totalParents) * 100) : 0,
                    ];
                })
                ->values(),
        ];

        return Inertia::render('Parents/Index', [
            'parents' => $parents,
            'filters' => [
                'search' => $filters['search'] ?? '',
                'relation' => $filters['relation'] ?? '',
                'class_name' => $filters['class_name'] ?? '',
                'status' => $filters['status'] ?? '',
            ],
            'parentStats' => $parentStats,
            'classNames' => ParentProfile::query()
                ->whereNotNull('class_name')
                ->where('class_name', '!=', '')
                ->distinct()
                ->orderBy('class_name')
                ->pluck('class_name')
                ->values()
                ->toArray(),
        ]);
    }

    public function store(StoreParentProfileRequest $request)
    {
        try {
            ParentProfile::create($request->validated());

            return back()->with('success', 'Parent created successfully.');
        } catch (Throwable $e) {
            Log::error('Parent create failed', [
                'message' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors([
                    'server' => 'Something went wrong while saving parent.',
                ]);
        }
    }

    public function update(UpdateParentProfileRequest $request, ParentProfile $parentProfile)
    {
        try {
            $parentProfile->update($request->validated());

            return back()->with('success', 'Parent updated successfully.');
        } catch (Throwable $e) {
            Log::error('Parent update failed', [
                'parent_id' => $parentProfile->id,
                'message' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors([
                    'edit_server' => 'Something went wrong while updating parent.',
                ]);
        }
    }

    public function destroy(ParentProfile $parentProfile)
    {
        try {
            $parentProfile->delete();

            return back()->with('success', 'Parent deleted successfully.');
        } catch (Throwable $e) {
            Log::error('Parent delete failed', [
                'parent_id' => $parentProfile->id,
                'message' => $e->getMessage(),
            ]);

            return back()->with('error', 'Something went wrong while deleting parent.');
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ]);

        try {
            $import = new ParentProfilesImport();

            Excel::import($import, $request->file('file'));

            if ($import->failures()->isNotEmpty()) {
                return back()->withErrors([
                    'import' => $import->failures()
                        ->take(10)
                        ->map(fn ($failure) => 'Row ' . $failure->row() . ': ' . implode(', ', $failure->errors()))
                        ->implode(' | '),
                ]);
            }

            return back()->with('success', 'Parents imported successfully.');
        } catch (Throwable $e) {
            Log::error('Parent import failed', [
                'message' => $e->getMessage(),
            ]);

            return back()->withErrors([
                'import' => 'Import failed. Please check your Excel file and try again.',
            ]);
        }
    }
}