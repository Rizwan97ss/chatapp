<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeInvoice extends Model
{
    protected $fillable = [
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
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'discount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_date' => 'date',
    ];

    public function getNetAmountAttribute()
    {
        return max($this->amount - $this->discount, 0);
    }

    public function getBalanceAttribute()
    {
        return max($this->net_amount - $this->paid_amount, 0);
    }
}