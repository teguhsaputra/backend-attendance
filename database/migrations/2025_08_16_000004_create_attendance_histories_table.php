<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('attendance_histories', function (Blueprint $table) {
            $table->id();
            $table->string('attendance_id', 100);
            $table->foreignId('employee_id')->constrained('employees')->onDelete('restrict');
            $table->timestamp('date_attendance');
            $table->tinyInteger('attendance_type'); // 1: In, 2: Out
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('attendance_histories');
    }
};