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
        Schema::create('admissions', function (Blueprint $table) {
    $table->id();

    $table->string('application_no')->unique();
    $table->string('student_name');
    $table->string('email')->nullable();
    $table->string('phone')->nullable();

    $table->enum('gender', ['Male', 'Female'])->nullable();
    $table->date('date_of_birth')->nullable();

    $table->string('applied_class');
    $table->string('previous_school')->nullable();

    $table->string('parent_name');
    $table->string('parent_phone');
    $table->string('parent_email')->nullable();

    $table->date('application_date')->nullable();
    $table->enum('status', ['New', 'Under Review', 'Approved', 'Rejected', 'Waitlisted'])->default('New');

    $table->text('address')->nullable();
    $table->text('remarks')->nullable();

    $table->timestamps();

    $table->index(['status', 'applied_class']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admissions');
    }
};
