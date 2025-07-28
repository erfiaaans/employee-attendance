<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Models\User;

class AttendanceController extends Controller
{
    public function index(Request $request, $id = null)
    {
        $filter = $request->input('filter', 'clock_in');
        $attendances = Attendance::when(['user', 'location'])
            ->orderBy('clock_in_time', 'desc')
            ->paginate(10)
            ->appends(['filter' => $filter]);
        $search = $request->input('search');
        $att = Attendance::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%")
                ->orWhere('position', 'like', "%{$search}%")
                ->orWhere('office_name', 'like', "%{$search}%");
        })
            ->orderBy('name')
            ->paginate(10)
            ->appends(['search' => $search]);

        $users = User::all();
        $allUsers = Attendance::all();
        return view('admin.attendance.index', compact('attendances', 'filter', 'allUsers'));
    }
}
