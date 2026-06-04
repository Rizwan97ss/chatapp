<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;

use App\Models\Student;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Throwable;
use App\Imports\StudentsImport;
use Maatwebsite\Excel\Facades\Excel;


class StudentController extends Controller
{
    public function index(Request $request)
{
    $filters = $request->only([
        'search',
        'class_name',
        'status',
    ]);

    $students = Student::query()
        ->select([
            'id',
            'admission_no',
            'full_name',
            'email',
            'phone',
            'gender',
            'date_of_birth',
            'class_name',
            'section',
            'parent_name',
            'parent_phone',
            'admission_date',
            'status',
            'address',
        ])
        ->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('full_name', 'like', "%{$search}%")
                    ->orWhere('admission_no', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('parent_name', 'like', "%{$search}%")
                    ->orWhere('parent_phone', 'like', "%{$search}%");
            });
        })
        ->when($filters['class_name'] ?? null, function ($query, $className) {
            $query->where('class_name', $className);
        })
        ->when($filters['status'] ?? null, function ($query, $status) {
            $query->where('status', $status);
        })
        ->latest()
        ->paginate(10)
        ->withQueryString();

    return Inertia::render('Students/Index', [
        'students' => $students,
        'filters' => $filters,
    ]);
}

    public function store(StoreStudentRequest $request)
    {
        try {
            Student::create($request->validated());

            return redirect()
                ->route('students.index')
                ->with('success', 'Student created successfully.');
        } catch (Throwable $e) {
            Log::error('Student create failed', [
                'message' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors([
                    'server' => 'Something went wrong while saving student. Please try again.',
                ]);
        }
    }
    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ]);

        try {
            $import = new StudentsImport();

            Excel::import($import, $request->file('file'));

            if ($import->failures()->isNotEmpty()) {
                return back()->withErrors([
                    'import' => $import->failures()
                        ->take(10)
                        ->map(fn($failure) => 'Row ' . $failure->row() . ': ' . implode(', ', $failure->errors()))
                        ->implode(' | '),
                ]);
            }

            return back()->with('success', 'Students imported successfully.');
        } catch (Throwable $e) {
            Log::error('Student import failed', [
                'message' => $e->getMessage(),
            ]);

            return back()->withErrors([
                'import' => 'Import failed. Please check your Excel file and try again.',
            ]);
        }
    }
    //update student
    public function update(UpdateStudentRequest $request, Student $student)
    {
        try {
            $student->update($request->validated());

            return redirect()
                ->route('students')
                ->with('success', 'Student updated successfully.');
        } catch (Throwable $e) {
            Log::error('Student update failed', [
                'student_id' => $student->id,
                'message' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors([
                    'edit_server' => 'Something went wrong while updating student. Please try again.',
                ]);
        }
    }
}
