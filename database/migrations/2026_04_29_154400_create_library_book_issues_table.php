<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

    public function up(): void
    {
        Schema::create('library_book_issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('library_book_id')->constrained()->cascadeOnDelete();

            $table->string('student_name');
            $table->string('admission_no')->nullable();
            $table->string('class_name')->nullable();

            $table->date('issue_date');
            $table->date('due_date')->nullable();

            $table->enum('status', ['Issued', 'Returned', 'Overdue'])->default('Issued');
            $table->text('notes')->nullable();

            $table->date('return_date')->nullable();

            $table->decimal('late_fee', 10, 2)->default(0);
            $table->decimal('lost_fee', 10, 2)->default(0);
            $table->decimal('broken_fee', 10, 2)->default(0);
            $table->decimal('total_fee', 10, 2)->default(0);
            $table->enum('fee_status', ['Paid', 'Pending'])->default('Pending');
            $table->string('payment_method')->nullable();
            $table->text('return_notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('library_book_issues');
    }
};
