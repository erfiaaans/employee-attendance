<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Location;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    //
    public function adminGate()
    {
        $today = now()->toDateString();

        $stats = (object) [
            'totalEmployees' => User::where('role', 'employee')->count(),
            'totalLocations' => Location::count(),
            'totalAttendances' => Attendance::count(),
            'todayAttendances' => Attendance::whereDate('clock_in_time', $today)
                ->distinct('user_id')
                ->count('user_id'),
            'todayAttendanceBoth' => Attendance::whereDate('clock_in_time', $today)
                ->whereDate('clock_out_time', $today)
                ->distinct('user_id')
                ->count('user_id'),
            'todayClockInOnly'    => Attendance::whereDate('clock_in_time', $today)
                ->distinct('user_id')->count('user_id'),
            'todayClockOutOnly'   => Attendance::whereDate('clock_out_time', $today)
                ->distinct('user_id')->count('user_id'),
        ];

        return view('admin.index', compact('stats'));
    }

    public function index()
    {
        $today = now()->toDateString();

        $stats = [
            'totalEmployees' => User::where('role', 'employee')->count(),
            'totalLocations' => Location::count(),
            'totalAttendances' => Attendance::count(),
            'todayAttendances' => Attendance::whereDate('clock_in_time', $today)
                ->distinct('user_id')
                ->count('user_id'),
        ];
        dd($stats);
        return view('admin.dashboard.index', compact('stats'));
    }
}
