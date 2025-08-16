<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceHistory;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AttendanceController extends Controller
{
    public function checkIn(Request $request)
    {
        try {
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

            return response()->json([
                'success' => true,
                'data' => $attendance,
                'message' => 'Absen masuk berhasil dicatat'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan absen masuk. Silakan coba lagi.'
            ], 500);
        }
    }

    public function checkOut(Request $request, $id)
    {
        try {
            $attendance = Attendance::findOrFail($id);
            $attendance->update(['clock_out' => now()]);

            AttendanceHistory::create([
                'attendance_id' => $attendance->attendance_id,
                'employee_id' => $attendance->employee_id,
                'date_attendance' => now(),
                'attendance_type' => 2,
                'description' => 'Absen Pulang',
            ]);

            return response()->json([
                'success' => true,
                'data' => $attendance,
                'message' => 'Absen pulang berhasil dicatat'
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data absen tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan absen pulang. Silakan coba lagi.'
            ], 500);
        }
    }

    public function logs(Request $request)
    {
        try {
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

            return response()->json([
                'success' => true,
                'data' => $attendances
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data absensi. Silakan coba lagi.'
            ], 500);
        }
    }

    public function logsByEmployee($employeeId)
    {
        try {
            $employee = Employee::with('departement')->findOrFail($employeeId);

            $attendances = Attendance::with('history')
                ->where('employee_id', $employeeId)
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'employee' => $employee,
                    'attendances' => $attendances,
                ]
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Karyawan tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data absensi karyawan. Silakan coba lagi.'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $attendance = Attendance::findOrFail($id);
            $attendance->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data absensi berhasil dihapus'
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data absensi tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data absensi. Silakan coba lagi.'
            ], 500);
        }
    }

    public function destroyByEmployee($employeeId)
    {
        try {
            $employee = Employee::findOrFail($employeeId);

            AttendanceHistory::whereIn('attendance_id', function($query) use ($employeeId) {
                $query->select('attendance_id')
                    ->from('attendances')
                    ->where('employee_id', $employeeId);
            })->delete();

            Attendance::where('employee_id', $employeeId)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Semua data absensi karyawan berhasil dihapus',
                'employee_id' => $employeeId
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Karyawan tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data absensi karyawan. Silakan coba lagi.'
            ], 500);
        }
    }
}