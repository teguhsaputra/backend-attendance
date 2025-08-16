<?php

namespace App\Http\Controllers;

use App\Models\Departement;
use Illuminate\Http\Request;

class DepartementController extends Controller
{
    public function index()
    {
        return Departement::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'departement_name' => 'required|string',
            'max_clock_in_time' => 'required',
            'max_clock_out_time' => 'required',
        ]);

        return Departement::create($request->all());
    }

    public function show($id)
    {
        return Departement::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $departement = Departement::findOrFail($id);
        $request->validate([
            'departement_name' => 'required|string',
            'max_clock_in_time' => 'nullable|date_format:H:i:s',
            'max_clock_out_time' => 'nullable|date_format:H:i:s'
        ]);

        $departement->update($request->only('departement_name','max_clock_in_time','max_clock_out_time'));

        return response()->json($departement);
    }

    public function destroy($id)
    {
        $departement = Departement::with('employees')->findOrFail($id);

        if ($departement->employees()->count() > 0) {
            return response()->json([
                'message' => 'Tidak bisa hapus departemen, masih ada employee yang menggunakan departemen ini.'
            ], 400);
        }

        $departement->delete();

        return response()->json(['message' => 'Departemen berhasil dihapus']);
    }

}
