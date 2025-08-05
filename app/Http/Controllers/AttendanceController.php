<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $userFilter = $request->input('filter');

        $attendances = Attendance::with(['user', 'location'])
            ->whereHas('user', function ($q) {
                $q->where('role', 'employee');
            })
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('user', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%")
                            ->orWhere('position', 'like', "%{$search}%");
                    })->orWhereHas('location', function ($q2) use ($search) {
                        $q2->where('office_name', 'like', "%{$search}%");
                    });

                    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $search)) {
                        $q->orWhereDate('clock_in_time', $search);
                    }
                });
            })
            ->when($userFilter, function ($query, $userFilter) {
                $query->whereHas('user', function ($q) use ($userFilter) {
                    $q->where('name', $userFilter);
                });
            })
            ->orderBy('clock_in_time', 'desc')
            ->paginate(10)
            ->appends([
                'filter' => $userFilter,
                'search' => $search,
            ]);

        $allUsers = User::where('role', 'employee')->get();

        return view('admin.attendance.index', compact('attendances', 'allUsers', 'userFilter', 'search'));
    }

    public function clockIn()
    {
        return view('employee.clock.clockin');
    }

    public function storeClockIn(Request $request)
    {
        $request->validate([
            'clock_in_latitude' => 'required|numeric',
            'clock_in_longitude' => 'required|numeric',
            'clock_in_photo' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $user = auth()->user();
        $photoPath = $request->file('clock_in_photo')->store('data/clock_in_photos', 'public');

        Attendance::create([
            'attendance_id' => Str::uuid(),
            'user_id' => $user->user_id,
            'location_id' => $user->location_id,
            'clock_in_time' => now(),
            'clock_in_latitude' => $request->input('clock_in_latitude'),
            'clock_in_longitude' => $request->input('clock_in_longitude'),
            'clock_in_photo_url' => $photoPath,
            'created_by' => $user->user_id,
        ]);

        return redirect()->route('employee.clock.clockin')->with('success', 'Clock In successful');
    }

    public function clockOut()
    {
        return view('employee.clock.clockout');
    }

    public function storeClockOut(Request $request)
    {
        $request->validate([
            'clock_out_latitude' => 'required|numeric',
            'clock_out_longitude' => 'required|numeric',
            'clock_out_photo_url' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $user = auth()->user();
        $photoPath = $request->file('clock_out_photo')->store('data/clock_out_photos', 'public');

        $attendance = Attendance::where('user_id', $user->user_id)
            ->whereDate('clock_in_time', now()->toDateString())
            ->whereNull('clock_out_time')
            ->latest()
            ->first();

        if (!$attendance) {
            return redirect()->route('employee.clock.clockout')->withErrors('Belum melakukan Clock In.');
        }
        $photoPath = $request->file('clock_out_photo')->store('data/clock_out_photos', 'public');
        $attendance->update([
            'clock_out_time' => now(),
            'clock_out_latitude' => $request->input('clock_out_latitude'),
            'clock_out_longitude' => $request->input('clock_out_longitude'),
            'clock_out_photo_url' => $photoPath,
            'created_by' => $user->user_id,
        ]);

        return redirect()->route('employee.clock.out')->with('success', 'Clock Out successful');
    }
}
