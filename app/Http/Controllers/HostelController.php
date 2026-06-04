<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHostelRequest;
use App\Http\Requests\UpdateHostelRequest;
use App\Imports\HostelsImport;
use App\Models\Hostel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class HostelController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only([
            'search',
            'type',
            'location',
            'status',
        ]);

        $hostels = Hostel::query()
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('hostel_no', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('type', 'like', "%{$search}%")
                        ->orWhere('warden', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%");
                });
            })
            ->when($filters['type'] ?? null, fn ($query, $type) => $query->where('type', $type))
            ->when($filters['location'] ?? null, fn ($query, $location) => $query->where('location', $location))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->latest()
            ->paginate(10)
            ->through(fn ($hostel) => [
                'id' => $hostel->id,
                'hostel_no' => $hostel->hostel_no,
                'name' => $hostel->name,
                'type' => $hostel->type,
                'warden' => $hostel->warden,
                'location' => $hostel->location,
                'rooms' => (int) $hostel->rooms,
                'capacity' => (int) $hostel->capacity,
                'occupied' => (int) $hostel->occupied,
                'vacant' => max((int) $hostel->capacity - (int) $hostel->occupied, 0),
                'occupancy_percentage' => $hostel->capacity > 0
                    ? round(($hostel->occupied / $hostel->capacity) * 100)
                    : 0,
                'status' => $hostel->status,
                'notes' => $hostel->notes,
            ])
            ->withQueryString();

        $totalCapacity = Hostel::sum('capacity');
        $totalOccupied = Hostel::sum('occupied');
        $totalRooms = Hostel::sum('rooms');
        $vacantBeds = max($totalCapacity - $totalOccupied, 0);

        $hostelStats = [
            'cards' => [
                ['label' => 'Total Hostels', 'value' => Hostel::count(), 'change' => 'All hostel records'],
                ['label' => 'Total Rooms', 'value' => $totalRooms, 'change' => 'Available rooms'],
                ['label' => 'Occupied Beds', 'value' => $totalOccupied, 'change' => 'Currently occupied'],
                ['label' => 'Vacant Beds', 'value' => $vacantBeds, 'change' => 'Available beds'],
            ],
            'overview' => [
                'total_hostels' => Hostel::count(),
                'rooms' => $totalRooms,
                'capacity' => $totalCapacity,
                'occupied' => $totalOccupied,
                'vacant' => $vacantBeds,
                'maintenance' => Hostel::where('status', 'Maintenance')->count(),
                'occupancy_percentage' => $totalCapacity > 0 ? round(($totalOccupied / $totalCapacity) * 100) : 0,
            ],
            'occupancyBars' => Hostel::query()
                ->select(['name', 'capacity', 'occupied'])
                ->get()
                ->map(fn ($hostel) => [
                    'label' => $hostel->name,
                    'value' => $hostel->capacity > 0
                        ? round(($hostel->occupied / $hostel->capacity) * 100)
                        : 0,
                ])
                ->values(),
        ];

        return Inertia::render('Hostels/Index', [
            'hostels' => $hostels,
            'filters' => [
                'search' => $filters['search'] ?? '',
                'type' => $filters['type'] ?? '',
                'location' => $filters['location'] ?? '',
                'status' => $filters['status'] ?? '',
            ],
            'hostelStats' => $hostelStats,
            'locations' => Hostel::query()
                ->whereNotNull('location')
                ->where('location', '!=', '')
                ->distinct()
                ->orderBy('location')
                ->pluck('location')
                ->values()
                ->toArray(),
        ]);
    }

    public function store(StoreHostelRequest $request)
    {
        try {
            Hostel::create($request->validated());

            return back()->with('success', 'Hostel created successfully.');
        } catch (Throwable $e) {
            Log::error('Hostel create failed', ['message' => $e->getMessage()]);

            return back()->withInput()->withErrors([
                'server' => 'Something went wrong while saving hostel.',
            ]);
        }
    }

    public function update(UpdateHostelRequest $request, Hostel $hostel)
    {
        try {
            $hostel->update($request->validated());

            return back()->with('success', 'Hostel updated successfully.');
        } catch (Throwable $e) {
            Log::error('Hostel update failed', [
                'hostel_id' => $hostel->id,
                'message' => $e->getMessage(),
            ]);

            return back()->withInput()->withErrors([
                'edit_server' => 'Something went wrong while updating hostel.',
            ]);
        }
    }

    public function destroy(Hostel $hostel)
    {
        try {
            $hostel->delete();

            return back()->with('success', 'Hostel deleted successfully.');
        } catch (Throwable $e) {
            Log::error('Hostel delete failed', [
                'hostel_id' => $hostel->id,
                'message' => $e->getMessage(),
            ]);

            return back()->with('error', 'Something went wrong while deleting hostel.');
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ]);

        try {
            $import = new HostelsImport();

            Excel::import($import, $request->file('file'));

            if ($import->failures()->isNotEmpty()) {
                return back()->withErrors([
                    'import' => $import->failures()
                        ->take(10)
                        ->map(fn ($failure) => 'Row ' . $failure->row() . ': ' . implode(', ', $failure->errors()))
                        ->implode(' | '),
                ]);
            }

            return back()->with('success', 'Hostels imported successfully.');
        } catch (Throwable $e) {
            Log::error('Hostel import failed', ['message' => $e->getMessage()]);

            return back()->withErrors([
                'import' => 'Import failed. Please check your Excel file and try again.',
            ]);
        }
    }
}