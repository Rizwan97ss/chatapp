<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAdmissionRequest;
use App\Http\Requests\UpdateAdmissionRequest;
use App\Models\Admission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Throwable;

class AdmissionController extends Controller
{
    public function create()
    {
        return Inertia::render('Admission/Apply');
    }

    public function publicStore(StoreAdmissionRequest $request)
    {
        try {
           
            Admission::create([
                ...$request->validated(),
                'application_no' => 'APP-' . now()->format('YmdHis') . '-' . strtoupper(substr(uniqid(), -5)),
                'status' => 'New',
                'application_date' => now()->toDateString(),
            ]);

            return redirect()
                ->route('admissions.apply')
                ->with('success', 'Your admission application has been submitted successfully.');
        } catch (Throwable $e) {
            Log::error('Public admission create failed', [
                'message' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors([
                    'server' => 'Something went wrong while submitting your application.',
                ]);
        }
    }
    public function index(Request $request)
    {
        $filters = $request->only([
            'search',
            'applied_class',
            'status',
        ]);

        $admissions = Admission::query()
            ->select([
                'id',
                'application_no',
                'student_name',
                'email',
                'phone',
                'gender',
                'date_of_birth',
                'applied_class',
                'previous_school',
                'parent_name',
                'parent_phone',
                'parent_email',
                'application_date',
                'status',
                'address',
                'remarks',
                'created_at',
            ])
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('student_name', 'like', "%{$search}%")
                        ->orWhere('application_no', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('parent_name', 'like', "%{$search}%")
                        ->orWhere('parent_phone', 'like', "%{$search}%");
                });
            })
            ->when($filters['applied_class'] ?? null, function ($query, $className) {
                $query->where('applied_class', $className);
            })
            ->when($filters['status'] ?? null, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $admissionStats = [
            'cards' => [
                [
                    'label' => 'Total Applications',
                    'value' => Admission::count(),
                    'note' => 'All admission applications',
                    'color' => 'from-indigo-500 to-sky-400',
                    'icon' => 'A',
                ],
                [
                    'label' => 'Under Review',
                    'value' => Admission::where('status', 'Under Review')->count(),
                    'note' => 'Applications being checked',
                    'color' => 'from-amber-500 to-orange-400',
                    'icon' => '!',
                ],
                [
                    'label' => 'Approved',
                    'value' => Admission::where('status', 'Approved')->count(),
                    'note' => 'Ready for student creation',
                    'color' => 'from-emerald-500 to-teal-400',
                    'icon' => '✓',
                ],
                [
                    'label' => 'Rejected',
                    'value' => Admission::where('status', 'Rejected')->count(),
                    'note' => 'Rejected applications',
                    'color' => 'from-rose-500 to-pink-400',
                    'icon' => '×',
                ],
            ],

            'overview' => [
                'new_applications' => Admission::where('status', 'New')->count(),
                'waitlisted' => Admission::where('status', 'Waitlisted')->count(),
                'with_parent_email' => Admission::whereNotNull('parent_email')->where('parent_email', '!=', '')->count(),
                'missing_parent_email' => Admission::where(function ($query) {
                    $query->whereNull('parent_email')->orWhere('parent_email', '');
                })->count(),
            ],

            'chart' => [
                'labels' => ['New', 'Review', 'Approved', 'Rejected', 'Waitlisted'],
                'data' => [
                    Admission::where('status', 'New')->count(),
                    Admission::where('status', 'Under Review')->count(),
                    Admission::where('status', 'Approved')->count(),
                    Admission::where('status', 'Rejected')->count(),
                    Admission::where('status', 'Waitlisted')->count(),
                ],
            ],
        ];

        return Inertia::render('Admission/Index', [
            'admissions' => $admissions,
            'filters' => $filters,
            'admissionStats' => $admissionStats,
        ]);
    }

    public function store(StoreAdmissionRequest $request)
    {
        try {
            Admission::create([
                ...$request->validated(),
                'application_no' => 'APP-' . now()->format('YmdHis') . '-' . strtoupper(substr(uniqid(), -5)),
                'status' => 'New',
                'application_date' => now()->toDateString(),
            ]);

            return redirect()
                ->route('admissions')
                ->with('success', 'Admission application created successfully.');
        } catch (Throwable $e) {
            Log::error('Admission create failed', [
                'message' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors([
                    'server' => 'Something went wrong while saving admission application.',
                ]);
        }
    }

    public function update(UpdateAdmissionRequest $request, Admission $admission)
    {
        try {
            $admission->update($request->validated());

            return redirect()
                ->route('admissions')
                ->with('success', 'Admission application updated successfully.');
        } catch (Throwable $e) {
            Log::error('Admission update failed', [
                'admission_id' => $admission->id,
                'message' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors([
                    'edit_server' => 'Something went wrong while updating admission application.',
                ]);
        }
    }
}
