<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreManagementRequest;
use App\Http\Requests\UpdateManagementRequest;
use App\Imports\ManagementImport;
use App\Models\Management;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class ManagementController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'role', 'department', 'status']);

        $management = Management::query()
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('member_no', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('role', 'like', "%{$search}%")
                        ->orWhere('department', 'like', "%{$search}%")
                        ->orWhere('authority', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($filters['role'] ?? null, fn($query, $role) => $query->where('role', $role))
            ->when($filters['department'] ?? null, fn($query, $department) => $query->where('department', $department))
            ->when($filters['status'] ?? null, fn($query, $status) => $query->where('status', $status))
            ->latest()
            ->paginate(10)
            ->through(fn($member) => [
                'id' => $member->id,
                'member_no' => $member->member_no,
                'name' => $member->name,
                'role' => $member->role,
                'department' => $member->department,
                'email' => $member->email,
                'phone' => $member->phone,
                'authority' => $member->authority,
                'status' => $member->status,
                'responsibilities' => $member->responsibilities,
            ])
            ->withQueryString();

        $totalMembers = Management::count();
        $activeMembers = Management::where('status', 'Active')->count();

        return Inertia::render('Administrations/Index', [
            'management' => $management,
            'filters' => [
                'search' => $filters['search'] ?? '',
                'role' => $filters['role'] ?? '',
                'department' => $filters['department'] ?? '',
                'status' => $filters['status'] ?? '',
            ],
            'managementStats' => [
                
                'overview' => [
                    'total' => $totalMembers,
                    'active' => $activeMembers,
                    'inactive' => Management::where('status', 'Inactive')->count(),
                    'departments' => Management::distinct('department')->count('department'),
                    'active_percentage' => $totalMembers > 0 ? round(($activeMembers / $totalMembers) * 100) : 0,
                ],
                'departmentBars' => Management::query()
                    ->selectRaw('department, COUNT(*) as total')
                    ->groupBy('department')
                    ->get()
                    ->map(fn($item) => [
                        'label' => $item->department,
                        'value' => $totalMembers > 0 ? round(($item->total / $totalMembers) * 100) : 0,
                    ])
                    ->values(),
            ],
            'roles' => Management::whereNotNull('role')->distinct()->orderBy('role')->pluck('role')->values(),
            'departments' => Management::whereNotNull('department')->distinct()->orderBy('department')->pluck('department')->values(),
        ]);
    }

    public function store(StoreManagementRequest $request)
    {
        try {
            Management::create($request->validated());

            return back()->with('success', 'Management member created successfully.');
        } catch (Throwable $e) {
            Log::error('Management create failed', ['message' => $e->getMessage()]);

            return back()->withInput()->withErrors([
                'server' => 'Something went wrong while saving management member.',
            ]);
        }
    }

    public function update(UpdateManagementRequest $request, Management $management)
    {
        try {
            $management->update($request->validated());

            return back()->with('success', 'Management member updated successfully.');
        } catch (Throwable $e) {
            Log::error('Management update failed', [
                'management_id' => $management->id,
                'message' => $e->getMessage(),
            ]);

            return back()->withInput()->withErrors([
                'edit_server' => 'Something went wrong while updating management member.',
            ]);
        }
    }

    public function destroy(Management $management)
    {
        try {
            $management->delete();

            return back()->with('success', 'Management member deleted successfully.');
        } catch (Throwable $e) {
            Log::error('Management delete failed', [
                'management_id' => $management->id,
                'message' => $e->getMessage(),
            ]);

            return back()->with('error', 'Something went wrong while deleting management member.');
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ]);

        try {
            $import = new ManagementImport();

            Excel::import($import, $request->file('file'));

            if ($import->failures()->isNotEmpty()) {
                return back()->withErrors([
                    'import' => $import->failures()
                        ->take(10)
                        ->map(fn($failure) => 'Row ' . $failure->row() . ': ' . implode(', ', $failure->errors()))
                        ->implode(' | '),
                ]);
            }

            return back()->with('success', 'Management members imported successfully.');
        } catch (Throwable $e) {
            Log::error('Management import failed', ['message' => $e->getMessage()]);

            return back()->withErrors([
                'import' => 'Import failed. Please check your Excel file and try again.',
            ]);
        }
    }
}
