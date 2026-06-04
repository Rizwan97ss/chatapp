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
        Schema::create('hostels', function (Blueprint $table) {
    $table->id();
    $table->string('hostel_no')->unique();
    $table->string('name');
    $table->enum('type', ['Boys', 'Girls', 'Staff / Guest']);
    $table->string('warden')->nullable();
    $table->string('location')->nullable();
    $table->unsignedInteger('rooms')->default(0);
    $table->unsignedInteger('capacity')->default(0);
    $table->unsignedInteger('occupied')->default(0);
    $table->enum('status', ['Active', 'Maintenance', 'Inactive'])->default('Active');
    $table->text('notes')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hostels');
    }
};
