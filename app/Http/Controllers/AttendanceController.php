<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Location;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $userFilter = $request->input('filter');

        $attendances = Attendance::orderBy('created_at', 'desc')->get();

        $allUsers = User::where('role', 'employee')->get();

        return view('admin.attendance.index', compact('attendances', 'allUsers', 'userFilter', 'search'));
    }
    public function handlePeriode(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);
        if ($request->action === 'export') {
            return $this->exportByPeriode($request);
        } elseif ($request->action === 'delete') {
            return $this->destroyByPeriode($request);
        } else {
            return $this->destroyByPeriode($request);
        }

        return back()->with('error', 'Aksi tidak dikenali.');
    }


    public function destroy($id)
    {
        $attendance = Attendance::findOrFail($id);
        if ($attendance->clock_in_photo_url && Storage::disk('public')->exists($attendance->clock_in_photo_url)) {
            Storage::disk('public')->delete($attendance->clock_in_photo_url);
        }
        if ($attendance->clock_out_photo_url && Storage::disk('public')->exists($attendance->clock_out_photo_url)) {
            Storage::disk('public')->delete($attendance->clock_out_photo_url);
        }
        $attendance->delete();
        return redirect()->back()->with('success', 'Presensi berhasil dihapus.');
    }
    public function destroyByPeriode(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);
        $attendances = Attendance::whereBetween('clock_in_time', [
            $request->start_date . ' 00:00:00',
            $request->end_date . ' 23:59:59',
        ])->get();
        foreach ($attendances as $attendance) {
            if ($attendance->clock_in_photo_url && Storage::disk('public')->exists($attendance->clock_in_photo_url)) {
                Storage::disk('public')->delete($attendance->clock_in_photo_url);
            }
            if ($attendance->clock_out_photo_url && Storage::disk('public')->exists($attendance->clock_out_photo_url)) {
                Storage::disk('public')->delete($attendance->clock_out_photo_url);
            }
            $attendance->delete();
        }
        return redirect()->route('admin.attendance') // atau 'admin.attendance'
            ->with('success', 'Riwayat Presensi periode ' . $request->start_date . ' sampai ' . $request->end_date . ' berhasil dihapus.');
    }
    public function exportByPeriode(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        $start = $request->start_date . ' 00:00:00';
        $end   = $request->end_date   . ' 23:59:59';

        $rows = Attendance::with(['user', 'location'])
            ->whereBetween('clock_in_time', [$start, $end])
            ->orderBy('clock_in_time', 'desc')
            ->get();
        $filename = 'attendance_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename=\"$filename\"",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0",
        ];

        $callback = function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, [
                'Nama Pegawai',
                'Tanggal',
                'Jam Masuk',
                'Jam Keluar',
                'Lokasi Masuk (lat,long)',
                'Lokasi Keluar (lat,long)',
            ]);

            foreach ($rows as $att) {
                $tgl  = $att->clock_in_time ? Carbon::parse($att->clock_in_time)->format('Y-m-d') : '';
                $jamIn  = $att->clock_in_time ? Carbon::parse($att->clock_in_time)->format('H:i') : '';
                $jamOut = $att->clock_out_time ? Carbon::parse($att->clock_out_time)->format('H:i') : '';
                $locIn  = trim(($att->clock_in_latitude ?? '') . ',' . ($att->clock_in_longitude ?? ''), ',');
                $locOut = trim(($att->clock_out_latitude ?? '') . ',' . ($att->clock_out_longitude ?? ''), ',');
                fputcsv($out, [
                    $att->user->name ?? '',
                    $tgl,
                    $jamIn,
                    $jamOut,
                    $locIn,
                    $locOut,
                ]);
            }
            fclose($out);
        };
        return new StreamedResponse($callback, 200, $headers);
    }
}
