<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFeeInvoiceRequest;
use App\Http\Requests\UpdateFeeInvoiceRequest;
use App\Models\FeeInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Throwable;

class FeeInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only([
            'search',
            'class_name',
            'fee_type',
            'status',
        ]);

        $feeRecords = FeeInvoice::query()
            ->select([
                'id',
                'invoice_no',
                'student_name',
                'admission_no',
                'class_name',
                'fee_type',
                'amount',
                'discount',
                'paid_amount',
                'due_date',
                'payment_method',
                'status',
                'notes',
                'created_at',
            ])
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('invoice_no', 'like', "%{$search}%")
                        ->orWhere('student_name', 'like', "%{$search}%")
                        ->orWhere('admission_no', 'like', "%{$search}%")
                        ->orWhere('class_name', 'like', "%{$search}%")
                        ->orWhere('fee_type', 'like', "%{$search}%");
                });
            })
            ->when($filters['class_name'] ?? null, function ($query, $className) {
                $query->where('class_name', $className);
            })
            ->when($filters['fee_type'] ?? null, function ($query, $feeType) {
                $query->where('fee_type', $feeType);
            })
            ->when($filters['status'] ?? null, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(10)
            ->through(fn ($invoice) => [
                'id' => $invoice->id,
                'invoice_no' => $invoice->invoice_no,
                'student_name' => $invoice->student_name,
                'admission_no' => $invoice->admission_no,
                'class_name' => $invoice->class_name,
                'fee_type' => $invoice->fee_type,
                'amount' => (float) $invoice->amount,
                'discount' => (float) $invoice->discount,
                'paid_amount' => (float) $invoice->paid_amount,
                'net_amount' => (float) $invoice->net_amount,
                'balance' => (float) $invoice->balance,
                'due_date' => optional($invoice->due_date)->format('Y-m-d'),
                'payment_method' => $invoice->payment_method,
                'status' => $invoice->status,
                'notes' => $invoice->notes,
            ])
            ->withQueryString();

        $totalAmount = FeeInvoice::sum('amount');
        $totalDiscount = FeeInvoice::sum('discount');
        $totalPaid = FeeInvoice::sum('paid_amount');
        $totalNet = max($totalAmount - $totalDiscount, 0);
        $pendingAmount = max($totalNet - $totalPaid, 0);

        $feeStats = [
            'cards' => [
                [
                    'label' => 'Total Collection',
                    'value' => 'AED ' . number_format($totalPaid, 2),
                    'change' => 'Collected amount',
                ],
                [
                    'label' => 'Pending Fees',
                    'value' => 'AED ' . number_format($pendingAmount, 2),
                    'change' => 'Remaining balance',
                ],
                [
                    'label' => 'Paid Students',
                    'value' => FeeInvoice::where('status', 'Paid')->count(),
                    'change' => 'Fully paid invoices',
                ],
                [
                    'label' => 'Overdue Invoices',
                    'value' => FeeInvoice::where('status', 'Overdue')->count(),
                    'change' => 'Need follow-up',
                ],
            ],
            'overview' => [
                'collected' => $totalPaid,
                'pending' => $pendingAmount,
                'discounts' => $totalDiscount,
                'total_net' => $totalNet,
                'collection_percentage' => $totalNet > 0 ? round(($totalPaid / $totalNet) * 100) : 0,
            ],
            'collectionBars' => FeeInvoice::query()
                ->selectRaw('fee_type, SUM(amount - discount) as total_net, SUM(paid_amount) as total_paid')
                ->groupBy('fee_type')
                ->get()
                ->map(function ($item) {
                    $percentage = $item->total_net > 0
                        ? round(($item->total_paid / $item->total_net) * 100)
                        : 0;

                    return [
                        'label' => $item->fee_type,
                        'value' => min($percentage, 100),
                    ];
                })
                ->values(),
        ];

        return Inertia::render('Fees/Index', [
            'feeRecords' => $feeRecords,
            'filters' => [
                'search' => $filters['search'] ?? '',
                'class_name' => $filters['class_name'] ?? '',
                'fee_type' => $filters['fee_type'] ?? '',
                'status' => $filters['status'] ?? '',
            ],
            'feeStats' => $feeStats,
            'classNames' => FeeInvoice::query()
                ->whereNotNull('class_name')
                ->where('class_name', '!=', '')
                ->distinct()
                ->orderBy('class_name')
                ->pluck('class_name')
                ->values()
                ->toArray(),
            'feeTypes' => FeeInvoice::query()
                ->whereNotNull('fee_type')
                ->where('fee_type', '!=', '')
                ->distinct()
                ->orderBy('fee_type')
                ->pluck('fee_type')
                ->values()
                ->toArray(),
        ]);
    }

    public function store(StoreFeeInvoiceRequest $request)
    {
        try {
            $data = $this->normalizeStatus($request->validated());

            FeeInvoice::create($data);

            return back()->with('success', 'Invoice created successfully.');
        } catch (Throwable $e) {
            Log::error('Fee invoice create failed', [
                'message' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors([
                    'server' => 'Something went wrong while saving invoice.',
                ]);
        }
    }

    public function update(UpdateFeeInvoiceRequest $request, FeeInvoice $feeInvoice)
    {
        try {
            $data = $this->normalizeStatus($request->validated());

            $feeInvoice->update($data);

            return back()->with('success', 'Invoice updated successfully.');
        } catch (Throwable $e) {
            Log::error('Fee invoice update failed', [
                'invoice_id' => $feeInvoice->id,
                'message' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors([
                    'edit_server' => 'Something went wrong while updating invoice.',
                ]);
        }
    }

    public function destroy(FeeInvoice $feeInvoice)
    {
        try {
            $feeInvoice->delete();

            return back()->with('success', 'Invoice deleted successfully.');
        } catch (Throwable $e) {
            Log::error('Fee invoice delete failed', [
                'invoice_id' => $feeInvoice->id,
                'message' => $e->getMessage(),
            ]);

            return back()->with('error', 'Something went wrong while deleting invoice.');
        }
    }

    private function normalizeStatus(array $data): array
    {
        $amount = (float) ($data['amount'] ?? 0);
        $discount = (float) ($data['discount'] ?? 0);
        $paid = (float) ($data['paid_amount'] ?? 0);
        $net = max($amount - $discount, 0);

        if ($paid >= $net && $net > 0) {
            $data['status'] = 'Paid';
        } elseif ($paid > 0) {
            $data['status'] = 'Partial';
        } elseif (!empty($data['due_date']) && $data['due_date'] < now()->toDateString()) {
            $data['status'] = 'Overdue';
        } else {
            $data['status'] = $data['status'] ?? 'Pending';
        }

        return $data;
    }
}