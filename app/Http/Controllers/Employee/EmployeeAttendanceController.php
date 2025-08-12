<?php

namespace App\Http\Controllers\Employee;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EmployeeAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth()->user();
        $attendancesRaw = Attendance::where('user_id', $user->user_id)
            ->when($request->search, function ($query, $search) {
                $query->whereDate('clock_in_time', 'like', "%{$search}%")
                    ->orWhereTime('clock_in_time', 'like', "%{$search}%");
            })
            ->orderBy('clock_in_time')
            ->get();

        $attendances = $attendancesRaw->groupBy(function ($item) {
            $date = $item->clock_in_time ?? $item->clock_out_time;
            return $date ? Carbon::parse($date)->format('Y-m-d') : 'unknown';
        });

        return view('employee.attendance.index', compact('attendances'));
    }
}
