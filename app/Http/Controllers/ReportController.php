<?php

namespace App\Http\Controllers;
use App\Models\Attendance;
use Illuminate\Http\Request;

class ReportController extends Controller {
    public function logs(Request $request) {
        $query = Attendance::with(['employee.departement']);

        if ($request->filled('date')) {
            $query->whereDate('clock_in', $request->date);
        }
        if ($request->filled('departement_id')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('departement_id', $request->departement_id);
            });
        }

        return $query->get()->map(function ($att) {
            $dept = $att->employee->departement;
            $onTimeIn = $att->clock_in <= now()->setTimeFromTimeString($dept->max_clock_in_time);
            $onTimeOut = $att->clock_out >= now()->setTimeFromTimeString($dept->max_clock_out_time);

            return [
                'employee' => $att->employee->name,
                'departement' => $dept->departement_name,
                'clock_in' => $att->clock_in,
                'clock_out' => $att->clock_out,
                'ontime_in' => $onTimeIn ? 'On Time' : 'Late',
                'ontime_out' => $onTimeOut ? 'On Time' : 'Early Leave',
            ];
        });
    }
}
