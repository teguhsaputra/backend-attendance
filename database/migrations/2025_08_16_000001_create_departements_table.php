<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('departements', function (Blueprint $table) {
            $table->id();
            $table->string('departement_name', 255);
            $table->time('max_clock_in_time');
            $table->time('max_clock_out_time');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('departements');
    }
};

// database/migrations/2025_08_16_000002_create_employees_table.php
return new class extends Migration {
    public function up(): void {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id', 50)->unique();
            $table->foreignId('departement_id')->constrained('departements')->onDelete('restrict');
            $table->string('name', 255);
            $table->text('address')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('employees');
    }
};