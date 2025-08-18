<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Location;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\AttendanceController;
use App\Models\OfficeLocationUser;

class ClockOutController extends Controller
{
    public function clockOut()
    {
        $user = auth()->user();
        $alreadyClockedOut = Attendance::where('user_id', $user->user_id)
            ->whereDate('clock_out_time', now()->toDateString())
            ->exists();
        $locations = OfficeLocationUser::with('locations')
            ->where('user_id', $user->user_id)
            ->get()
            ->pluck('locations')
            ->filter()
            ->values();
        if ($locations->isEmpty()) {
            return redirect()
                ->route('employee.clock.clockout')
                ->withErrors('Lokasi untuk user ini tidak ditemukan.');
        }
        return view('employee.clock.clockout', [
            'locations' => $locations,
            'alreadyClockedOut' => $alreadyClockedOut,
        ]);
    }
    public function storeClockOut(Request $request)
    {
        $request->validate([
            'location_id' => 'required|exists:locations,location_id',
            'clock_out_latitude' => 'required|numeric',
            'clock_out_longitude' => 'required|numeric',
            'clock_out_photo' => 'required|string',
        ]);
        $user = auth()->user();
        $location = Location::find($request->input('location_id'));
        $radius = $location->radius_meters ?? 50;
        $distance = $this->calculateDistance(
            $request->input('clock_out_latitude'),
            $request->input('clock_out_longitude'),
            $location->latitude,
            $location->longitude
        );
        if ($distance > $radius) {
            return back()->with('error', 'Clock-out ditolak! Anda berada di luar radius kantor (' . round($distance, 2) . ' m).');
        }
        $photoPath = $this->saveBase64ImageToAttendancePath(
            $request->clock_out_photo,
            $user->user_id,
            'clockout',
            disk: 'public'
        );
        Attendance::create([
            'attendance_id' => Str::uuid(),
            'user_id' => $user->user_id,
            'location_id' => $request->input('location_id'),
            'clock_out_time' => now(),
            'clock_out_latitude' => $request->input('clock_out_latitude'),
            'clock_out_longitude' => $request->input('clock_out_longitude'),
            'clock_out_photo_url' => $photoPath,
            'created_by' => $user->user_id,
        ]);
        return redirect()->route('employee.clock.clockout')->with('success', 'Clock Out successful');
    }
    private function saveBase64ImageToAttendancePath(string $dataUrl, string $userId, string $type = 'clockout', string $disk = 'public'): string
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
