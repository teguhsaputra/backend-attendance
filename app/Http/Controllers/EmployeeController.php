<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        return Employee::with('departement')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|string|unique:employees',
            'departement_id' => 'required|exists:departements,id',
            'name' => 'required|string',
            'address' => 'required|string',
        ]);

        return Employee::create($request->all());
    }

    public function show($id)
    {
        return Employee::with('departement')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);
        $request->validate([
            'name' => 'required|string',
            'departement_id' => 'required|exists:departements,id',
            'address' => 'nullable|string'
        ]);

        $employee->update($request->only('name','departement_id','address'));

        return response()->json($employee);
    }

    public function destroy($id)
    {
        $emp = Employee::findOrFail($id);
        $emp->delete();
        return response()->json(['message' => 'Employee deleted']);
    }
}
