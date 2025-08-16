<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EmployeeController extends Controller
{
    public function index()
    {
        try {
            $employees = Employee::with('departement')->get();
            return response()->json([
                'success' => true,
                'data' => $employees
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data karyawan. Silakan coba lagi.'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'departement_id' => 'required|exists:departements,id',
                'name' => 'required|string',
                'address' => 'required|string',
            ]);

            // Generate employee_id automatically
            $lastEmployee = Employee::orderBy('id', 'desc')->first();
            $lastId = $lastEmployee ? $lastEmployee->id : 0;
            $employeeId = 'EMP-' . ($lastId + 1);

            $employee = Employee::create([
                'employee_id' => $employeeId,
                'departement_id' => $request->departement_id,
                'name' => $request->name,
                'address' => $request->address
            ]);
            
            return response()->json([
                'success' => true,
                'data' => $employee,
                'message' => 'Karyawan berhasil ditambahkan'
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan karyawan. Silakan coba lagi.'
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $employee = Employee::with('departement')->findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $employee
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Karyawan tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data karyawan. Silakan coba lagi.'
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $employee = Employee::findOrFail($id);
            $request->validate([
                'name' => 'required|string',
                'departement_id' => 'required|exists:departements,id',
                'address' => 'nullable|string'
            ]);

            $employee->update($request->only('name','departement_id','address'));

            return response()->json([
                'success' => true,
                'data' => $employee,
                'message' => 'Data karyawan berhasil diperbarui'
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Karyawan tidak ditemukan'
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data karyawan. Silakan coba lagi.'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $employee = Employee::findOrFail($id);
            $employee->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Karyawan berhasil dihapus'
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Karyawan tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus karyawan. Silakan coba lagi.'
            ], 500);
        }
    }
}