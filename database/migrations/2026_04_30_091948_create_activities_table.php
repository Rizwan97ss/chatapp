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
        Schema::create('activities', function (Blueprint $table) {
    $table->id();
    $table->string('activity_no')->unique();
    $table->string('title');
    $table->string('type')->nullable();
    $table->string('class_name')->nullable();
    $table->date('activity_date')->nullable();
    $table->time('activity_time')->nullable();
    $table->string('venue')->nullable();
    $table->string('organizer')->nullable();
    $table->enum('status', ['Upcoming', 'Completed', 'Cancelled'])->default('Upcoming');
    $table->text('description')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
