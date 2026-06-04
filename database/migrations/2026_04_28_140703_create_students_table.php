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
        Schema::create('students', function (Blueprint $table) {
            $table->id();

            $table->string('admission_no')->unique();
            $table->string('full_name');
            $table->string('email')->nullable()->unique();
            $table->string('phone')->nullable();

            $table->enum('gender', ['Male', 'Female'])->nullable();
            $table->date('date_of_birth')->nullable();

            $table->string('class_name');
            $table->string('section')->nullable();

            $table->string('parent_name');
            $table->string('parent_phone');

            $table->date('admission_date')->nullable();
            $table->enum('status', ['Active', 'Pending', 'Inactive'])->default('Active');

            $table->text('address')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
