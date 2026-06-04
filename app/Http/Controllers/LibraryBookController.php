<?php

// app/Http/Controllers/LibraryBookController.php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLibraryBookRequest;
use App\Http\Requests\UpdateLibraryBookRequest;
use App\Models\LibraryBook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Throwable;
use App\Models\LibraryBookIssue;
use App\Imports\LibraryBooksImport;
use Maatwebsite\Excel\Facades\Excel;
class LibraryBookController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only([
            'search',
            'category',
            'shelf',
            'status',
        ]);
        $issueRecords = LibraryBookIssue::query()
            ->with('book:id,book_no,title')
            ->latest()
            ->paginate(10, ['*'], 'issues_page')
            ->withQueryString();
        $books = LibraryBook::query()
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('book_no', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%")
                        ->orWhere('author', 'like', "%{$search}%")
                        ->orWhere('isbn', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%");
                });
            })
            ->when($filters['category'] ?? null, fn($query, $category) => $query->where('category', $category))
            ->when($filters['shelf'] ?? null, fn($query, $shelf) => $query->where('shelf', $shelf))
            ->when($filters['status'] ?? null, fn($query, $status) => $query->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $totalBooks = LibraryBook::sum('copies');
        $issuedBooks = LibraryBook::where('status', 'Issued')->sum('copies');
        $overdueBooks = LibraryBook::where('status', 'Overdue')->sum('copies');
        $availableBooks = LibraryBook::query()
            ->whereDoesntHave('issues', function ($query) {
                $query->whereIn('status', ['Issued', 'Overdue']);
            })
            ->orderBy('title')
            ->get(['id', 'book_no', 'title', 'author', 'category']);
            $countAvailableBooks = $availableBooks->count();

        $libraryStats = [
            'cards' => [
                [
                    'label' => 'Total Books',
                    'value' => number_format($totalBooks),
                    'change' => 'Total copies',
                ],
                [
                    'label' => 'Issued Books',
                    'value' => number_format($issuedBooks),
                    'change' => 'Currently issued',
                ],
                [
                    'label' => 'Available Books',
                    'value' => number_format($countAvailableBooks),
                    'change' => 'Ready to issue',
                ],
                [
                    'label' => 'Overdue Books',
                    'value' => number_format($overdueBooks),
                    'change' => 'Need follow-up',
                ],
            ],
            'overview' => [
                'total' => $totalBooks,
                'issued' => $issuedBooks,
                'available' => 100,
                'overdue' => $overdueBooks,
                // 'available_percentage' => $totalBooks > 0 ? round(($countAvailableBooks / $totalBooks) * 100) : 0,
                'available_percentage' => 100,
            ],
            'categoryBars' => LibraryBook::query()
                ->selectRaw('category, SUM(copies) as total_copies')
                ->whereNotNull('category')
                ->groupBy('category')
                ->get()
                ->map(function ($item) use ($totalBooks) {
                    return [
                        'label' => $item->category,
                        'value' => $totalBooks > 0 ? round(($item->total_copies / $totalBooks) * 100) : 0,
                    ];
                })
                ->values(),
        ];

        return Inertia::render('Library/Index', [
            'books' => $books,
            'issueRecords' => $issueRecords,
            'availableBooks' => $availableBooks,
            'filters' => [
                'search' => $filters['search'] ?? '',
                'category' => $filters['category'] ?? '',
                'shelf' => $filters['shelf'] ?? '',
                'status' => $filters['status'] ?? '',
            ],
            'libraryStats' => $libraryStats,
            'categories' => LibraryBook::query()
                ->whereNotNull('category')
                ->where('category', '!=', '')
                ->distinct()
                ->orderBy('category')
                ->pluck('category')
                ->values()
                ->toArray(),
            'shelves' => LibraryBook::query()
                ->whereNotNull('shelf')
                ->where('shelf', '!=', '')
                ->distinct()
                ->orderBy('shelf')
                ->pluck('shelf')
                ->values()
                ->toArray(),
        ]);
    }
    public function issueBook(Request $request)
    {
        $data = $request->validate([
            'library_book_id' => ['required', 'exists:library_books,id'],
            'student_name' => ['required', 'string', 'max:255'],
            'admission_no' => ['nullable', 'string', 'max:255'],
            'class_name' => ['nullable', 'string', 'max:255'],
            'issue_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:issue_date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $alreadyIssued = LibraryBookIssue::where('library_book_id', $data['library_book_id'])
            ->whereIn('status', ['Issued', 'Overdue'])
            ->exists();

        if ($alreadyIssued) {
            return back()->withErrors([
                'library_book_id' => 'This book is already issued and cannot be issued again until returned.',
            ]);
        }

        $data['status'] = !empty($data['due_date']) && $data['due_date'] < now()->toDateString()
            ? 'Overdue'
            : 'Issued';

        LibraryBookIssue::create($data);

        return back()->with('success', 'Book issued successfully.');
    }

    public function returnBook(Request $request, LibraryBookIssue $issue)
    {
        $data = $request->validate([
            'late_fee' => ['nullable', 'numeric', 'min:0'],
            'lost_fee' => ['nullable', 'numeric', 'min:0'],
            'broken_fee' => ['nullable', 'numeric', 'min:0'],
            'fee_status' => ['required', 'in:Paid,Pending'],
            'payment_method' => ['nullable', 'string', 'max:255'],
            'return_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $lateFee = (float) ($data['late_fee'] ?? 0);
        $lostFee = (float) ($data['lost_fee'] ?? 0);
        $brokenFee = (float) ($data['broken_fee'] ?? 0);

        $issue->update([
            'return_date' => now()->toDateString(),
            'status' => 'Returned',
            'late_fee' => $lateFee,
            'lost_fee' => $lostFee,
            'broken_fee' => $brokenFee,
            'total_fee' => $lateFee + $lostFee + $brokenFee,
            'fee_status' => $data['fee_status'],
            'payment_method' => $data['payment_method'] ?? null,
            'return_notes' => $data['return_notes'] ?? null,
        ]);

        return back()->with('success', 'Book returned successfully.');
    }

    public function deleteIssue(LibraryBookIssue $issue)
    {
        $issue->delete();

        return back()->with('success', 'Issue record deleted successfully.');
    }
    public function store(StoreLibraryBookRequest $request)
    {
        try {
            LibraryBook::create($request->validated());

            return back()->with('success', 'Book created successfully.');
        } catch (Throwable $e) {
            Log::error('Book create failed', [
                'message' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors([
                    'server' => 'Something went wrong while saving book.',
                ]);
        }
    }

    public function update(UpdateLibraryBookRequest $request, LibraryBook $libraryBook)
    {
        try {
            $libraryBook->update($request->validated());

            return back()->with('success', 'Book updated successfully.');
        } catch (Throwable $e) {
            Log::error('Book update failed', [
                'book_id' => $libraryBook->id,
                'message' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors([
                    'edit_server' => 'Something went wrong while updating book.',
                ]);
        }
    }
public function import(Request $request)
{
    $request->validate([
        'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
    ]);

    try {
        $import = new LibraryBooksImport();

        Excel::import($import, $request->file('file'));

        if ($import->failures()->isNotEmpty()) {
            return back()->withErrors([
                'import' => $import->failures()
                    ->take(10)
                    ->map(fn ($failure) => 'Row ' . $failure->row() . ': ' . implode(', ', $failure->errors()))
                    ->implode(' | '),
            ]);
        }

        return back()->with('success', 'Books imported successfully.');
    } catch (Throwable $e) {
        Log::error('Books import failed', [
            'message' => $e->getMessage(),
        ]);

        return back()->withErrors([
            'import' => 'Import failed. Please check your Excel file and try again.',
        ]);
    }
}
    public function destroy(LibraryBook $libraryBook)
    {
        try {
            $libraryBook->delete();

            return back()->with('success', 'Book deleted successfully.');
        } catch (Throwable $e) {
            Log::error('Book delete failed', [
                'book_id' => $libraryBook->id,
                'message' => $e->getMessage(),
            ]);

            return back()->with('error', 'Something went wrong while deleting book.');
        }
    }
}
