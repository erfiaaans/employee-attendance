<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Location;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

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
    public function destroy($id)
    {
        // dd($id);
        $attendance = Attendance::findOrFail($id);
        if ($attendance->clock_in_photo_url && Storage::disk('public')->exists($attendance->clock_in_photo_url)) {
            Storage::disk('public')->delete($attendance->clock_in_photo_url);
        }
        if ($attendance->clock_out_photo_url && Storage::disk('public')->exists($attendance->clock_out_photo_url)) {
            Storage::disk('public')->delete($attendance->clock_out_photo_url);
        }
        $attendance->delete();
        // return redirect()->route('admin.attendance.index')->with('success', 'Attendance record deleted successfully.');
        return redirect()->back()->with('success', 'Presensi berhasil dihapus.');
    }
}
