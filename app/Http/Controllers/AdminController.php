<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Location;
use App\Models\Attendance;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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


    public function adminStats(Request $request)
    {
        $from = $request->filled('from')
            ? Carbon::parse($request->input('from'))->startOfDay()
            : now()->copy()->subDays(13)->startOfDay();

        $to = $request->filled('to')
            ? Carbon::parse($request->input('to'))->endOfDay()
            : now()->endOfDay();

        $today = now()->toDateString();

        // KPI dasar
        $totalEmployees   = User::where('role', 'employee')->count();
        $totalLocations   = Location::count();
        $totalAttendances = Attendance::count();

        // Hari ini
        $todayClockInOnly  = Attendance::whereDate('clock_in_time', $today)
            ->distinct('user_id')->count('user_id');
        $todayClockOutOnly = Attendance::whereDate('clock_out_time', $today)
            ->distinct('user_id')->count('user_id');
        $todayAttendanceBoth = Attendance::whereDate('clock_in_time', $today)
            ->whereDate('clock_out_time', $today)
            ->distinct('user_id')->count('user_id');

        // Rata-rata jam clock-in (di range)
        $avgClockInSec = Attendance::whereBetween('clock_in_time', [$from, $to])
            ->avg(DB::raw('TIME_TO_SEC(TIME(clock_in_time))'));
        $avgClockIn = $avgClockInSec ? gmdate('H:i', (int) $avgClockInSec) : null;

        // Terlambat (patokan 09:00:00 – sesuaikan)
        $lateCount = Attendance::whereBetween('clock_in_time', [$from, $to])
            ->whereTime('clock_in_time', '>', '09:00:00')
            ->distinct('user_id')->count('user_id');

        // Tren 14 hari (atau range filter) – distinct user clock-in per hari
        $daily = Attendance::selectRaw('DATE(clock_in_time) as d, COUNT(DISTINCT user_id) as cnt')
            ->whereBetween('clock_in_time', [$from, $to])
            ->groupBy('d')->orderBy('d')->get()->pluck('cnt', 'd')->toArray();

        $labels = [];
        $values = [];
        $cursor = $from->copy();
        while ($cursor <= $to) {
            $key = $cursor->toDateString();
            $labels[] = $cursor->format('d M');
            $values[] = (int) ($daily[$key] ?? 0);
            $cursor->addDay();
        }

        // Per lokasi (bulan berjalan)
        $monthStart = now()->startOfMonth();
        $byLocationRaw = Attendance::select('location_id', DB::raw('COUNT(DISTINCT user_id) as cnt'))
            ->whereBetween('clock_in_time', [$monthStart, now()])
            ->groupBy('location_id')
            ->get();

        // Ambil nama lokasi (minim kueri: sekali fetch semua Location)
        $locationsMap = Location::pluck('office_name', 'location_id');
        $byLocation = $byLocationRaw->map(function ($r) use ($locationsMap) {
            return [
                'label' => $locationsMap[$r->location_id] ?? ('Lokasi ' . $r->location_id),
                'value' => (int) $r->cnt,
            ];
        })->values();

        return response()->json([
            // KPI dasar
            'totalEmployees'     => $totalEmployees,
            'totalLocations'     => $totalLocations,
            'totalAttendances'   => $totalAttendances,

            // Hari ini
            'todayAttendances'   => $todayClockInOnly,
            'todayAttendanceBoth' => $todayAttendanceBoth,
            'todayClockInOnly'   => $todayClockInOnly,
            'todayClockOutOnly'  => $todayClockOutOnly,

            // Ketepatan waktu
            'avgClockIn'         => $avgClockIn,
            'lateCount'          => $lateCount,

            // Grafik
            'series'             => ['labels' => $labels, 'values' => $values],
            'byLocation'         => $byLocation,
        ]);
    }
}
