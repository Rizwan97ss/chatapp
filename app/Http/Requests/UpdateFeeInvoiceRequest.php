<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFeeInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $invoiceId = $this->route('feeInvoice')?->id;

        return [
            'invoice_no' => [
                'required',
                'string',
                'max:50',
                Rule::unique('fee_invoices', 'invoice_no')->ignore($invoiceId),
            ],
            'student_name' => ['required', 'string', 'max:255'],
            'admission_no' => ['nullable', 'string', 'max:100'],
            'class_name' => ['required', 'string', 'max:100'],
            'fee_type' => ['required', 'string', 'max:100'],
            'amount' => ['required', 'numeric', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
            'due_date' => ['nullable', 'date'],
            'payment_method' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'string', 'in:Pending,Paid,Partial,Overdue'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}