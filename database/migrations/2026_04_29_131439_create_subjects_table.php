<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('subject_id')->unique();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('class_name');
            $table->string('teacher_name')->nullable();
            $table->string('type')->default('Core');
            $table->unsignedInteger('weekly_hours')->default(0);
            $table->unsignedInteger('credit')->nullable();
            $table->string('status')->default('Active');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};