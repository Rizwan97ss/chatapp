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
       Schema::create('transports', function (Blueprint $table) {
    $table->id();
    $table->string('vehicle_no')->unique();
    $table->string('vehicle');
    $table->string('type')->default('Bus');
    $table->string('route');
    $table->string('driver')->nullable();
    $table->string('phone')->nullable();
    $table->string('plate')->unique();
    $table->unsignedInteger('capacity')->default(0);
    $table->unsignedInteger('students')->default(0);
    $table->string('status')->default('Active');
    $table->text('notes')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transports');
    }
};
