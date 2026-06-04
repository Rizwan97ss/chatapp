<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_invoices', function (Blueprint $table) {
            $table->id();

            $table->string('invoice_no')->unique();
            $table->string('student_name');
            $table->string('admission_no')->nullable();
            $table->string('class_name');
            $table->string('fee_type');

            $table->decimal('amount', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('paid_amount', 10, 2)->default(0);

            $table->date('due_date')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('status')->default('Pending');

            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_invoices');
    }
};