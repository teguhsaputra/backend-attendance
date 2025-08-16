<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DepartementController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AttendanceController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Semua endpoint API untuk Departement, Employee, dan Attendance.
|
*/

// Departement CRUD
Route::prefix('departements')->group(function () {
    Route::get('/', [DepartementController::class, 'index']);     // List Departement
    Route::post('/', [DepartementController::class, 'store']);    // Tambah Departement
    Route::get('/{id}', [DepartementController::class, 'show']);  // Detail Departement
    Route::put('/{id}', [DepartementController::class, 'update']); // Update Departement
    Route::delete('/{id}', [DepartementController::class, 'destroy']); // Hapus Departement
});

// Employee CRUD
Route::prefix('employees')->group(function () {
    Route::get('/', [EmployeeController::class, 'index']);     // List Employee
    Route::post('/', [EmployeeController::class, 'store']);    // Tambah Employee
    Route::get('/{id}', [EmployeeController::class, 'show']);  // Detail Employee
    Route::put('/{id}', [EmployeeController::class, 'update']); // Update Employee
    Route::delete('/{id}', [EmployeeController::class, 'destroy']); // Hapus Employee
});

// Attendance
Route::prefix('attendance')->group(function () {
    Route::post('/check-in', [AttendanceController::class, 'checkIn']); // Absen Masuk
    Route::put('/check-out/{id}', [AttendanceController::class, 'checkOut']); // Absen Keluar
    Route::get('/logs', [AttendanceController::class, 'logs']);
    Route::get('/logs/{employeeId}', [AttendanceController::class, 'logsByEmployee']);
    Route::delete('/logs/{employeeId}', [AttendanceController::class, 'destroyByEmployee']);
});
