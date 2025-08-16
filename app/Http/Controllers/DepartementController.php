<?php

namespace App\Http\Controllers;

use App\Models\Departement;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DepartementController extends Controller
{
    public function index()
    {
        try {
            return Departement::all();
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data departemen. Silakan coba lagi.'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'departement_name' => 'required|string',
                'max_clock_in_time' => 'required',
                'max_clock_out_time' => 'required',
            ]);

            $departement = Departement::create($request->all());
            
            return response()->json([
                'success' => true,
                'data' => $departement,
                'message' => 'Departemen berhasil dibuat'
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
                'message' => 'Gagal membuat departemen. Silakan coba lagi.'
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $departement = Departement::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $departement
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Departemen tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data departemen. Silakan coba lagi.'
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $departement = Departement::findOrFail($id);
            $request->validate([
                'departement_name' => 'required|string',
                'max_clock_in_time' => 'nullable|date_format:H:i:s',
                'max_clock_out_time' => 'nullable|date_format:H:i:s'
            ]);

            $departement->update($request->only('departement_name','max_clock_in_time','max_clock_out_time'));

            return response()->json([
                'success' => true,
                'data' => $departement,
                'message' => 'Departemen berhasil diperbarui'
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Departemen tidak ditemukan'
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
                'message' => 'Gagal memperbarui departemen. Silakan coba lagi.'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $departement = Departement::with('employees')->findOrFail($id);

            if ($departement->employees()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak bisa menghapus departemen karena masih terdapat karyawan yang terdaftar.'
                ], 400);
            }

            $departement->delete();

            return response()->json([
                'success' => true,
                'message' => 'Departemen berhasil dihapus'
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Departemen tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus departemen. Silakan coba lagi.'
            ], 500);
        }
    }
}