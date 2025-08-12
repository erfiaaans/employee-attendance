<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Controllers\UserLocationController;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Location;
use App\Models\OfficeLocationUser;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ClockInController extends Controller
{
    public function clockIn()
    {
        $user = auth()->user();
        $alreadyClockedIn = Attendance::where('user_id', $user->user_id)
            ->whereDate('clock_in_time', now()->toDateString())
            ->exists();
        $locations = OfficeLocationUser::with('locations')
            ->where('user_id', $user->user_id)
            ->get()
            ->pluck('locations')
            ->filter()
            ->values();
        if ($locations->isEmpty()) {
            return redirect()
                ->route('employee.clock.clockin')
                ->withErrors('Lokasi untuk user ini tidak ditemukan.');
        }

        return view('employee.clock.clockin', [
            'locations' => $locations,
            'alreadyClockedIn' => $alreadyClockedIn,
        ]);
    }
    public function storeClockIn(Request $request)
    {
        $request->validate([
            'location_id'        => 'required|exists:locations,location_id',
            'clock_in_latitude'  => 'required|numeric',
            'clock_in_longitude' => 'required|numeric',
            'clock_in_photo'     => 'required|string',
        ]);

        $user = auth()->user();
        $location = Location::findOrFail($request->input('location_id'));

        // radius bisa ambil dari DB; fallback 50m
        $radius = $location->radius_meters ?? 50;

        $distance = $this->calculateDistance(
            $request->input('clock_in_latitude'),
            $request->input('clock_in_longitude'),
            $location->latitude,
            $location->longitude
        );

        if ($distance > $radius) {
            return back()->with('error', 'Clock-in ditolak! Di luar radius kantor (' . round($distance, 2) . ' m).');
        }

        $photoPath = $this->saveBase64ImageToAttendancePath(
            $request->clock_in_photo,
            $user->user_id,
            'clockin',
            disk: 'public'
        );

        Attendance::create([
            'attendance_id'       => Str::uuid(),
            'user_id'             => $user->user_id,
            'location_id'         => $request->input('location_id'),
            'clock_in_time'       => now(),
            'clock_in_latitude'   => $request->input('clock_in_latitude'),
            'clock_in_longitude'  => $request->input('clock_in_longitude'),
            'clock_in_photo_url'  => $photoPath,
            'created_by'          => $user->user_id,
        ]);

        return redirect()->route('employee.clock.clockin')->with('success', 'Clock In successful');
    }
    private function saveBase64ImageToAttendancePath(string $dataUrl, string $userId, string $type = 'clockin', string $disk = 'public'): string
    {
        if (!str_starts_with($dataUrl, 'data:')) {
            throw new \InvalidArgumentException('Invalid data URL');
        }

        [$meta, $content] = explode(',', $dataUrl, 2);
        preg_match('#data:(image/\w+);base64#', $meta, $m);
        $mime = $m[1] ?? 'image/png';
        $ext  = str_contains($mime, 'jpeg') ? 'jpg' : explode('/', $mime)[1];

        $binary = base64_decode($content);
        if ($binary === false) {
            throw new \RuntimeException('Gagal decode gambar');
        }

        $now = now();
        $dir = sprintf('attendance/%s/%s', $userId, $now->format('Y/m/d'));
        $filename = sprintf('%s-%s.%s', $type, $now->format('Hisv'), $ext); // Hisv = jammenitdetik+millis
        $path = $dir . '/' . $filename;

        Storage::disk($disk)->put($path, $binary, [
            'visibility' => 'private',
            'ContentType' => $mime,
        ]);

        return $path;
    }

    private function calculateDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371000; // meter
        $latFrom = deg2rad($lat1);
        $lngFrom = deg2rad($lng1);
        $latTo = deg2rad($lat2);
        $lngTo = deg2rad($lng2);
        $latDelta = $latTo - $latFrom;
        $lngDelta = $lngTo - $lngFrom;
        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lngDelta / 2), 2)));
        return $earthRadius * $angle;
    }
}
