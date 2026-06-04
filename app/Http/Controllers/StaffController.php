<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStaffRequest;
use App\Http\Requests\UpdateStaffRequest;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Throwable;
use App\Imports\StaffsImport;
use Maatwebsite\Excel\Facades\Excel;
class StaffController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only([
            'search',
            'department',
            'shift',
            'status',
        ]);

        $staffs = Staff::query()
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('staff_no', 'like', "%{$search}%")
                        ->orWhere('full_name', 'like', "%{$search}%")
                        ->orWhere('role', 'like', "%{$search}%")
                        ->orWhere('department', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($filters['department'] ?? null, fn ($query, $department) => $query->where('department', $department))
            ->when($filters['shift'] ?? null, fn ($query, $shift) => $query->where('shift', $shift))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->latest()
            ->paginate(10)
            ->through(fn ($staff) => [
                'id' => $staff->id,
                'staff_no' => $staff->staff_no,
                'full_name' => $staff->full_name,
                'role' => $staff->role,
                'department' => $staff->department,
                'phone' => $staff->phone,
                'email' => $staff->email,
                'shift' => $staff->shift,
                'salary' => (float) $staff->salary,
                'joining_date' => optional($staff->joining_date)->format('Y-m-d'),
                'status' => $staff->status,
                'address' => $staff->address,
            ])
            ->withQueryString();

        $totalStaff = Staff::count();
        $activeStaff = Staff::where('status', 'Active')->count();

        $staffStats = [
            'cards' => [
                [
                    'label' => 'Total Staffs',
                    'value' => $totalStaff,
                    'change' => 'All staff records',
                ],
                [
                    'label' => 'Active Staffs',
                    'value' => $activeStaff,
                    'change' => 'Currently active',
                ],
                [
                    'label' => 'On Leave',
                    'value' => Staff::where('status', 'On Leave')->count(),
                    'change' => 'Temporarily unavailable',
                ],
                [
                    'label' => 'Inactive Staffs',
                    'value' => Staff::where('status', 'Inactive')->count(),
                    'change' => 'Inactive records',
                ],
            ],
            'overview' => [
                'total' => $totalStaff,
                'active' => $activeStaff,
                'on_leave' => Staff::where('status', 'On Leave')->count(),
                'inactive' => Staff::where('status', 'Inactive')->count(),
                'active_percentage' => $totalStaff > 0 ? round(($activeStaff / $totalStaff) * 100) : 0,
                'night_shift' => Staff::where('shift', 'Night')->count(),
            ],
            'departmentBars' => Staff::query()
                ->selectRaw('department, COUNT(*) as total')
                ->whereNotNull('department')
                ->where('department', '!=', '')
                ->groupBy('department')
                ->get()
                ->map(function ($item) use ($totalStaff) {
                    return [
                        'label' => $item->department,
                        'value' => $totalStaff > 0 ? round(($item->total / $totalStaff) * 100) : 0,
                    ];
                })
                ->values(),
        ];

        return Inertia::render('Staffs/Index', [
            'staffs' => $staffs,
            'filters' => [
                'search' => $filters['search'] ?? '',
                'department' => $filters['department'] ?? '',
                'shift' => $filters['shift'] ?? '',
                'status' => $filters['status'] ?? '',
            ],
            'staffStats' => $staffStats,
            'departments' => Staff::query()
                ->whereNotNull('department')
                ->where('department', '!=', '')
                ->distinct()
                ->orderBy('department')
                ->pluck('department')
                ->values()
                ->toArray(),
            'roles' => Staff::query()
                ->whereNotNull('role')
                ->where('role', '!=', '')
                ->distinct()
                ->orderBy('role')
                ->pluck('role')
                ->values()
                ->toArray(),
        ]);
    }

    public function store(StoreStaffRequest $request)
    {
        try {
            Staff::create($request->validated());

            return back()->with('success', 'Staff created successfully.');
        } catch (Throwable $e) {
            Log::error('Staff create failed', [
                'message' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors([
                    'server' => 'Something went wrong while saving staff.',
                ]);
        }
    }

    public function update(UpdateStaffRequest $request, Staff $staff)
    {
        try {
            $staff->update($request->validated());

            return back()->with('success', 'Staff updated successfully.');
        } catch (Throwable $e) {
            Log::error('Staff update failed', [
                'staff_id' => $staff->id,
                'message' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors([
                    'edit_server' => 'Something went wrong while updating staff.',
                ]);
        }
    }
public function import(Request $request)
{
    $request->validate([
        'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
    ]);

    try {
        $import = new StaffsImport();

        Excel::import($import, $request->file('file'));

        if ($import->failures()->isNotEmpty()) {
            return back()->withErrors([
                'import' => $import->failures()
                    ->take(10)
                    ->map(fn ($failure) => 'Row ' . $failure->row() . ': ' . implode(', ', $failure->errors()))
                    ->implode(' | '),
            ]);
        }

        return back()->with('success', 'Staffs imported successfully.');
    } catch (Throwable $e) {
        Log::error('Staff import failed', [
            'message' => $e->getMessage(),
        ]);

        return back()->withErrors([
            'import' => 'Import failed. Please check your Excel file and try again.',
        ]);
    }
}
    public function destroy(Staff $staff)
    {
        try {
            $staff->delete();

            return back()->with('success', 'Staff deleted successfully.');
        } catch (Throwable $e) {
            Log::error('Staff delete failed', [
                'staff_id' => $staff->id,
                'message' => $e->getMessage(),
            ]);

            return back()->with('error', 'Something went wrong while deleting staff.');
        }
    }
}