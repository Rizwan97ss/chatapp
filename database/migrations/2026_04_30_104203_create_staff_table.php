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
        Schema::create('staff', function (Blueprint $table) {
    $table->id();
    $table->string('staff_no')->unique();
    $table->string('full_name');
    $table->string('role')->nullable();
    $table->string('department')->nullable();
    $table->string('phone')->nullable();
    $table->string('email')->nullable()->unique();
    $table->enum('shift', ['Morning', 'Evening', 'Night'])->default('Morning');
    $table->decimal('salary', 10, 2)->default(0);
    $table->date('joining_date')->nullable();
    $table->enum('status', ['Active', 'On Leave', 'Inactive'])->default('Active');
    $table->text('address')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
