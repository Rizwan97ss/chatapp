<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('management', function (Blueprint $table) {
            $table->id();
            $table->string('member_no')->unique();
            $table->string('name');
            $table->string('role');
            $table->string('department');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('authority')->nullable();
            $table->string('status')->default('Active');
            $table->text('responsibilities')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('management');
    }
};