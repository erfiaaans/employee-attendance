<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Location;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
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
        $request->validate(['start_date' => 'required|date', 'end_date' => 'required|date|after_or_equal:start_date',]);
        if ($request->action === 'export') {
            return $this->exportByPeriode($request);
        } elseif ($request->action === 'delete') {
            return $this->destroyByPeriode($request);
        } else {
            return $this->destroyByPeriode($request);
        }
        // return back()->with('error', 'Aksi tidak dikenali.');
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

        // Nama file: sertakan periode
        $startLabel = Carbon::parse($request->start_date)->format('Y-m-d');
        $endLabel   = Carbon::parse($request->end_date)->format('Y-m-d');
        $timestamp  = now()->format('Y-m-d_H-i-s');
        $filename   = "attendance_{$startLabel}_to_{$endLabel}_{$timestamp}.xlsx";

        // Helper Haversine (meter)
        $haversine = function (?float $lat1, ?float $lon1, ?float $lat2, ?float $lon2): ?float {
            if ($lat1 === null || $lon1 === null || $lat2 === null || $lon2 === null) {
                return null;
            }
            $earth = 6371000; // meter
            $dLat = deg2rad($lat2 - $lat1);
            $dLon = deg2rad($lon2 - $lon1);
            $rlat1 = deg2rad($lat1);
            $rlat2 = deg2rad($lat2);
            $a = sin($dLat / 2) * sin($dLat / 2) +
                sin($dLon / 2) * sin($dLon / 2) * cos($rlat1) * cos($rlat2);
            $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
            return $earth * $c;
        };

        $callback = function () use ($rows, $haversine) {
            if (ob_get_length()) {
                @ob_end_clean();
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Attendance');

            // Header (Kantor setelah Nama; tambah Jam Keluar)
            // A  B              C       D        E          F           G                           H                           I            J            K                     L
            // No | Nama Pegawai | Kantor| Tanggal| Jam Masuk| Jam Keluar| Lokasi Masuk (lat,long) | Lokasi Keluar (lat,long) | Foto Masuk | Foto Keluar | Radius Check In (m) | Radius Check Out (m)
            $headers = [
                'A1' => 'No',
                'B1' => 'Nama Pegawai',
                'C1' => 'Kantor',
                'D1' => 'Tanggal',
                'E1' => 'Jam Masuk',
                'F1' => 'Jam Keluar',                 // <- ditambahkan
                'G1' => 'Lokasi Masuk (lat,long)',
                'H1' => 'Lokasi Keluar (lat,long)',
                'I1' => 'Foto Masuk',
                'J1' => 'Foto Keluar',
                'K1' => 'Radius Check In (m)',
                'L1' => 'Radius Check Out (m)',
            ];
            foreach ($headers as $cell => $text) {
                $sheet->setCellValue($cell, $text);
            }
            $sheet->getStyle('A1:L1')->getFont()->setBold(true);
            $sheet->freezePane('A2');

            // Data
            $rowNum = 2;
            $no = 1;

            foreach ($rows as $att) {
                // Tanggal: fallback ke clock_out jika clock_in kosong
                $tgl = $att->clock_in_time
                    ? Carbon::parse($att->clock_in_time)->format('Y-m-d')
                    : ($att->clock_out_time ? Carbon::parse($att->clock_out_time)->format('Y-m-d') : '');

                $jamIn  = $att->clock_in_time  ? Carbon::parse($att->clock_in_time)->format('H:i')   : '';
                $jamOut = $att->clock_out_time ? Carbon::parse($att->clock_out_time)->format('H:i')  : '';

                $locIn  = trim(($att->clock_in_latitude  ?? '') . ',' . ($att->clock_in_longitude  ?? ''), ',');
                $locOut = trim(($att->clock_out_latitude ?? '') . ',' . ($att->clock_out_longitude ?? ''), ',');

                $officeName = $att->location?->office_name ?? '-';
                $officeLat  = $att->location?->latitude;
                $officeLon  = $att->location?->longitude;

                // Foto (pakai accessor yang sudah dibuat)
                $hasPhotoIn  = method_exists($att, 'getHasClockInPhotoAttribute')  ? $att->has_clock_in_photo  : null;
                $hasPhotoOut = method_exists($att, 'getHasClockOutPhotoAttribute') ? $att->has_clock_out_photo : null;
                $photoInUrl  = $att->clock_in_photo_path  ?? null;
                $photoOutUrl = $att->clock_out_photo_path ?? null;

                // Radius (meter) dari kantor
                $radiusInVal  = $haversine($att->clock_in_latitude,  $att->clock_in_longitude,  $officeLat, $officeLon);
                $radiusOutVal = $haversine($att->clock_out_latitude, $att->clock_out_longitude, $officeLat, $officeLon);

                // Tulis data utama
                $sheet->setCellValue("A{$rowNum}", $no);
                $sheet->setCellValue("B{$rowNum}", $att->user->name ?? '');
                $sheet->setCellValue("C{$rowNum}", $officeName);
                $sheet->setCellValue("D{$rowNum}", $tgl);
                $sheet->setCellValue("E{$rowNum}", $jamIn ?: '');
                $sheet->setCellValue("F{$rowNum}", $jamOut ?: '');   // <- jam keluar ditulis di F
                $sheet->setCellValue("G{$rowNum}", $locIn  ?: '');
                $sheet->setCellValue("H{$rowNum}", $locOut ?: '');

                // Hyperlink foto masuk -> I
                if ($hasPhotoIn === null) {
                    $rawIn = $att->clock_in_photo_url ?? null; // path relatif di disk public
                    $has   = $rawIn && Storage::disk('public')->exists($rawIn);
                    if ($has && $photoInUrl) {
                        $sheet->setCellValue("I{$rowNum}", "Lihat Foto");
                        $sheet->getCell("I{$rowNum}")->getHyperlink()->setUrl($photoInUrl)->setTooltip('Klik untuk melihat foto masuk');
                    } else {
                        $sheet->setCellValue("I{$rowNum}", "-");
                    }
                } else {
                    if ($hasPhotoIn && $photoInUrl) {
                        $sheet->setCellValue("I{$rowNum}", "Lihat Foto");
                        $sheet->getCell("I{$rowNum}")->getHyperlink()->setUrl($photoInUrl)->setTooltip('Klik untuk melihat foto masuk');
                    } else {
                        $sheet->setCellValue("I{$rowNum}", "-");
                    }
                }

                // Hyperlink foto keluar -> J
                if ($hasPhotoOut === null) {
                    $rawOut = $att->clock_out_photo_url ?? null;
                    $has    = $rawOut && Storage::disk('public')->exists($rawOut);
                    if ($has && $photoOutUrl) {
                        $sheet->setCellValue("J{$rowNum}", "Lihat Foto");
                        $sheet->getCell("J{$rowNum}")->getHyperlink()->setUrl($photoOutUrl)->setTooltip('Klik untuk melihat foto keluar');
                    } else {
                        $sheet->setCellValue("J{$rowNum}", "-");
                    }
                } else {
                    if ($hasPhotoOut && $photoOutUrl) {
                        $sheet->setCellValue("J{$rowNum}", "Lihat Foto");
                        $sheet->getCell("J{$rowNum}")->getHyperlink()->setUrl($photoOutUrl)->setTooltip('Klik untuk melihat foto keluar');
                    } else {
                        $sheet->setCellValue("J{$rowNum}", "-");
                    }
                }

                // Radius (angka supaya bisa difilter/hitung)
                $sheet->setCellValue("K{$rowNum}", $radiusInVal  !== null ? round($radiusInVal, 2)  : '-');
                $sheet->setCellValue("L{$rowNum}", $radiusOutVal !== null ? round($radiusOutVal, 2) : '-');

                $rowNum++;
                $no++;
            }

            // Autosize kolom (A..L)
            foreach (range('A', 'L') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // AutoFilter baris header
            $sheet->setAutoFilter("A1:L1");

            (new Xlsx($spreadsheet))->save('php://output');
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);
        };

        $headers = [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control'       => 'max-age=0, no-cache, must-revalidate, proxy-revalidate',
            'Pragma'              => 'public',
            'Expires'             => '0',
        ];

        return new StreamedResponse($callback, 200, $headers);
    }


    function haversine($lat1, $lon1, $lat2, $lon2)
    {
        $earth = 6371000; // meter
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $lat1 = deg2rad($lat1);
        $lat2 = deg2rad($lat2);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            sin($dLon / 2) * sin($dLon / 2) * cos($lat1) * cos($lat2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earth * $c;
    }
}
