<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceHistory;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AttendanceController extends Controller
{
    
    public function checkIn(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
        ]);

        $attId = Str::uuid();

        $attendance = Attendance::create([
            'employee_id' => $request->employee_id,
            'attendance_id' => $attId,
            'clock_in' => now(),
        ]);

        AttendanceHistory::create([
            'attendance_id' => $attId,
            'employee_id' => $request->employee_id,
            'date_attendance' => now(),
            'attendance_type' => 1,
            'description' => 'Absen Masuk',
        ]);

        return $attendance;
    }

    public function checkOut(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);
        $attendance->update(['clock_out' => now()]);

        AttendanceHistory::create([
            'attendance_id' => $attendance->attendance_id,
            'employee_id' => $attendance->employee_id,
            'date_attendance' => now(),
            'attendance_type' => 2,
            'description' => 'Absen Pulang',
        ]);

        return $attendance;
    }

    public function logs(Request $request)
    {
        $query = Attendance::with('employee.departement');

        if ($request->has('date')) {
            $query->whereDate('clock_in', $request->date);
        }

        if ($request->has('department_id')) {
            $query->whereHas('employee', function($q) use ($request) {
                $q->where('departement_id', $request->department_id);
            });
        }

        $attendances = $query->get();

        $attendances->map(function ($item) {
            $maxIn  = $item->employee->departement->max_clock_in_time ?? '08:00:00';
            $maxOut = $item->employee->departement->max_clock_out_time ?? '17:00:00';

            $item->status_in  = $item->clock_in > date('Y-m-d').' '.$maxIn ? 'Terlambat' : 'Tepat Waktu';
            $item->status_out = $item->clock_out < date('Y-m-d').' '.$maxOut ? 'Pulang Lebih Awal' : 'Tepat Waktu';

            return $item;
        });

        return $attendances;
    }



    public function logsByEmployee($employeeId)
    {
        $employee = Employee::with('departement')->findOrFail($employeeId);

        $attendances = Attendance::with('history')
            ->where('employee_id', $employeeId)
            ->get();

        return response()->json([
            'employee' => $employee,
            'attendances' => $attendances,
        ]);
    }

    public function destroy($id)
    {
        $attendance = Attendance::findOrFail($id);
        $attendance->delete();

        return response()->json(['message' => 'Attendance berhasil dihapus']);
    }

    public function destroyByEmployee($employeeId)
    {
        $employee = Employee::findOrFail($employeeId);

        AttendanceHistory::whereIn('attendance_id', function($query) use ($employeeId) {
            $query->select('attendance_id')
                ->from('attendances')
                ->where('employee_id', $employeeId);
        })->delete();

        Attendance::where('employee_id', $employeeId)->delete();

        return response()->json([
            'message' => 'Semua logs absensi untuk employee berhasil dihapus',
            'employee_id' => $employeeId
        ]);
    }

}
