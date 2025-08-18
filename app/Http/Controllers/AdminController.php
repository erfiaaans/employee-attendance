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
        return view('admin.index');
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
        return view('admin.dashboard.index', compact('stats'));
    }
}
