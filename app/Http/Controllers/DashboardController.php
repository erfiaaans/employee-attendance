<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Location;
use App\Models\Attendance;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = $this->computeStat();
        $locations = Location::orderBy('office_name', 'asc')->get();

        return view('admin.dashboard', compact('stats', 'locations'));
    }
    public function stats()
    {
        return response()->json($this->computerStat());
    }
    private function computerStats()
    {
        $todayJkt = Carbon::now('Asia/Jakarta');
        $todayAttendance = Attendance::whereNotNull('clock_in_time')
            ->whereDate('clock_in_time', $todayJkt->toDateString())
            ->count();
        return [
            'totalEmployees'  => User::count(),
            'totalLocations'  => Location::count(),
            'totalAttendance' => Attendance::count(),
            'todayAttendance' => $todayAttendance,
        ];
    }
}
