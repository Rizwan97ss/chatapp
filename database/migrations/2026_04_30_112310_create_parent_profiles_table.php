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
        Schema::create('parent_profiles', function (Blueprint $table) {
    $table->id();
    $table->string('parent_no')->unique();
    $table->string('full_name');
    $table->enum('relation', ['Father', 'Mother', 'Guardian']);
    $table->string('student_name');
    $table->string('class_name');
    $table->string('phone');
    $table->string('email')->nullable()->unique();
    $table->string('occupation')->nullable();
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
        Schema::dropIfExists('parent_profiles');
    }
};
