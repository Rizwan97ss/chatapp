<?php
// database/migrations/xxxx_create_school_classes_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_classes', function (Blueprint $table) {
            $table->id();
            $table->string('class_id')->unique();
            $table->string('name');
            $table->string('section');
            $table->string('teacher_name')->nullable();
            $table->string('room')->nullable();
            $table->unsignedInteger('capacity')->nullable();
            $table->unsignedInteger('students_count')->default(0);
            $table->string('status')->default('Active');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['name', 'section']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_classes');
    }
};