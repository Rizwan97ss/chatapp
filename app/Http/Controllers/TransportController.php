<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransportRequest;
use App\Http\Requests\UpdateTransportRequest;
use App\Models\Transport;
use App\Imports\TransportsImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Throwable;

class TransportController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'route', 'type', 'status']);

        $transports = Transport::query()
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('vehicle_no', 'like', "%{$search}%")
                        ->orWhere('vehicle', 'like', "%{$search}%")
                        ->orWhere('route', 'like', "%{$search}%")
                        ->orWhere('driver', 'like', "%{$search}%")
                        ->orWhere('plate', 'like', "%{$search}%");
                });
            })
            ->when($filters['route'] ?? null, fn ($query, $route) => $query->where('route', $route))
            ->when($filters['type'] ?? null, fn ($query, $type) => $query->where('type', $type))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->latest()
            ->paginate(10)
            ->through(fn ($transport) => [
                'id' => $transport->id,
                'vehicle_no' => $transport->vehicle_no,
                'vehicle' => $transport->vehicle,
                'type' => $transport->type,
                'route' => $transport->route,
                'driver' => $transport->driver,
                'phone' => $transport->phone,
                'plate' => $transport->plate,
                'capacity' => (int) $transport->capacity,
                'students' => (int) $transport->students,
                'available' => max((int) $transport->capacity - (int) $transport->students, 0),
                'usage_percentage' => $transport->capacity > 0
                    ? round(($transport->students / $transport->capacity) * 100)
                    : 0,
                'status' => $transport->status,
                'notes' => $transport->notes,
            ])
            ->withQueryString();

        $totalCapacity = Transport::sum('capacity');
        $totalStudents = Transport::sum('students');

        return Inertia::render('Transport/Index', [
            'transports' => $transports,
            'filters' => [
                'search' => $filters['search'] ?? '',
                'route' => $filters['route'] ?? '',
                'type' => $filters['type'] ?? '',
                'status' => $filters['status'] ?? '',
            ],
            'transportStats' => [
                'cards' => [
                    ['label' => 'Total Vehicles', 'value' => Transport::count(), 'change' => 'All vehicle records'],
                    ['label' => 'Active Routes', 'value' => Transport::distinct('route')->count('route'), 'change' => 'Current routes'],
                    ['label' => 'Assigned Students', 'value' => $totalStudents, 'change' => 'Currently assigned'],
                    ['label' => 'Maintenance Due', 'value' => Transport::where('status', 'Maintenance')->count(), 'change' => 'Vehicles in maintenance'],
                ],
                'overview' => [
                    'capacity' => $totalCapacity,
                    'students' => $totalStudents,
                    'available' => max($totalCapacity - $totalStudents, 0),
                    'active' => Transport::where('status', 'Active')->count(),
                    'maintenance' => Transport::where('status', 'Maintenance')->count(),
                    'usage_percentage' => $totalCapacity > 0 ? round(($totalStudents / $totalCapacity) * 100, 1) : 0,
                ],
                'routeUsage' => Transport::query()
                    ->selectRaw('route, SUM(capacity) as capacity, SUM(students) as students')
                    ->groupBy('route')
                    ->get()
                    ->map(fn ($item) => [
                        'label' => $item->route,
                        'value' => $item->capacity > 0 ? round(($item->students / $item->capacity) * 100) : 0,
                    ])
                    ->values(),
            ],
            'routes' => Transport::query()
                ->whereNotNull('route')
                ->where('route', '!=', '')
                ->distinct()
                ->orderBy('route')
                ->pluck('route')
                ->values(),
        ]);
    }

    public function store(StoreTransportRequest $request)
    {
        try {
            Transport::create($request->validated());

            return back()->with('success', 'Transport vehicle created successfully.');
        } catch (Throwable $e) {
            Log::error('Transport create failed', ['message' => $e->getMessage()]);

            return back()->withInput()->withErrors([
                'server' => 'Something went wrong while saving transport vehicle.',
            ]);
        }
    }

    public function update(UpdateTransportRequest $request, Transport $transport)
    {
        try {
            $transport->update($request->validated());

            return back()->with('success', 'Transport vehicle updated successfully.');
        } catch (Throwable $e) {
            Log::error('Transport update failed', [
                'transport_id' => $transport->id,
                'message' => $e->getMessage(),
            ]);

            return back()->withInput()->withErrors([
                'edit_server' => 'Something went wrong while updating transport vehicle.',
            ]);
        }
    }
public function import(Request $request)
{
    $request->validate([
        'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
    ]);

    try {
        $import = new TransportsImport();

        Excel::import($import, $request->file('file'));

        if ($import->failures()->isNotEmpty()) {
            return back()->withErrors([
                'import' => $import->failures()
                    ->take(10)
                    ->map(fn ($failure) => 'Row ' . $failure->row() . ': ' . implode(', ', $failure->errors()))
                    ->implode(' | '),
            ]);
        }

        return back()->with('success', 'Transport vehicles imported successfully.');
    } catch (Throwable $e) {
        Log::error('Transport import failed', ['message' => $e->getMessage()]);

        return back()->withErrors([
            'import' => 'Import failed. Please check your Excel file and try again.',
        ]);
    }
}
    public function destroy(Transport $transport)
    {
        try {
            $transport->delete();

            return back()->with('success', 'Transport vehicle deleted successfully.');
        } catch (Throwable $e) {
            Log::error('Transport delete failed', [
                'transport_id' => $transport->id,
                'message' => $e->getMessage(),
            ]);

            return back()->with('error', 'Something went wrong while deleting transport vehicle.');
        }
    }
}