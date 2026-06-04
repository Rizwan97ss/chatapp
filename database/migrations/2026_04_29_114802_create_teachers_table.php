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
         Schema::create('teachers', function (Blueprint $table) {
        $table->id();
        $table->string('teacher_id')->unique();
        $table->string('full_name');
        $table->string('email')->nullable()->unique();
        $table->string('phone')->nullable();
        $table->string('gender')->nullable();
        $table->date('date_of_birth')->nullable();
        $table->string('subject');
        $table->string('assigned_class')->nullable();
        $table->date('joining_date')->nullable();
        $table->string('status')->default('Active');
        $table->text('address')->nullable();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
