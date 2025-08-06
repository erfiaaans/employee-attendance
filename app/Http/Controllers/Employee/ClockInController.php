<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Location;
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
        $location = Location::first();
        if (!$location) {
            return redirect()->route('employee.clock.clockin')->withErrors('Lokasi tidak ditemukan.');
        }
        return view('employee.clock.clockin', compact('location', 'alreadyClockedIn'));
    }
    public function storeClockIn(Request $request)
    {
        $request->validate([
            'location_id' => 'required|exists:locations,location_id',
            'clock_in_latitude' => 'required|numeric',
            'clock_in_longitude' => 'required|numeric',
            'clock_in_photo' => 'required|string',
        ]);
        $user = auth()->user();
        $location = Location::find($request->input('location_id'));
        $distance = $this->calculateDistance(
            $request->input('clock_in_latitude'),
            $request->input('clock_in_longitude'),
            $location->latitude,
            $location->longitude
        );
        if ($distance > 50) {
            return back()->with('error', 'Clock-in ditolak! Anda berada di luar radius kantor (' . round($distance, 2) . ' m).');
        }
        $photoPath = $this->saveBase64Image($request->clock_in_photo, 'data/clock_in_photos');
        Attendance::create([
            'attendance_id' => Str::uuid(),
            'user_id' => $user->user_id,
            'location_id' => $request->input('location_id'),
            'clock_in_time' => now(),
            'clock_in_latitude' => $request->input('clock_in_latitude'),
            'clock_in_longitude' => $request->input('clock_in_longitude'),
            'clock_in_photo_url' => $photoPath,
            'created_by' => $user->user_id,
        ]);

        return redirect()->route('employee.clock.clockin')->with('success', 'Clock In successful');
    }
    private function saveBase64Image($base64Image, $folder)
    {
        @list($type, $fileData) = explode(';', $base64Image);
        @list(, $fileData) = explode(',', $fileData);
        $extension = 'png';
        if (strpos($type, 'jpeg') || strpos($type, 'jpg')) {
            $extension = 'jpg';
        }
        $filename = uniqid() . '.' . $extension;
        $path = $folder . '/' . $filename;
        Storage::disk('public')->put($path, base64_decode($fileData));
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
