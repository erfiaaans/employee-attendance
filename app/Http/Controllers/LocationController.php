<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\OfficeLocationUser;
use Illuminate\Database\QueryException;

class LocationController extends Controller
{
    public function index(Request $request, $id = null)
    {
        $search = $request->input('search');

        $locations = Location::when($search, function ($query, $search) {
            return $query->where('office_name', 'like', "%{$search}%")
                ->orWhere('address', 'like', "%{$search}%")
                ->orWhere('city', 'like', "%{$search}%");
        })
            ->orderBy('office_name')
            ->paginate(10)
            ->appends(['search' => $search]);

        $editLocation = null;
        if ($id) {
            $editLocation = Location::where('location_id', $id)->first();
        }

        return view('admin.location.index', compact('locations', 'editLocation'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'office_name'    => 'required|string|max:100',
                'address'        => 'required|string|max:255',
                'city'           => 'required|string|max:100',
                'latitude'       => 'required|numeric|between:-90,90',
                'longitude'      => 'required|numeric|between:-180,180',
                'radius'         => 'required|numeric|min:1',
                'check_in_time'  => ['required', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
                'check_out_time' => ['required', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
            ]);

            $in  = $this->normalizeTime($request->input('check_in_time'));
            $out = $this->normalizeTime($request->input('check_out_time'));

            if ($in && $out && !$this->isOutAfterIn($in, $out)) {
                return back()->withInput()->withErrors([
                    'check_out_time' => 'Jam check-out harus setelah jam check-in.'
                ]);
            }

            $location = new Location();
            $location->location_id     = uniqid();
            $location->office_name     = $validated['office_name'];
            $location->address         = $validated['address'];
            $location->city            = $validated['city'];
            $location->latitude        = $validated['latitude'];
            $location->longitude       = $validated['longitude'];
            $location->radius          = $validated['radius'];
            $location->check_in_time   = $in;
            $location->check_out_time  = $out;
            $location->save();

            return redirect()->back()->with('success', 'Kantor berhasil ditambahkan!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data. Pastikan semua data diisi dengan benar.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'office_name'    => 'required|string|max:100',
                'address'        => 'required|string|max:255',
                'city'           => 'required|string|max:100',
                'latitude'       => 'required|numeric|between:-90,90',
                'longitude'      => 'required|numeric|between:-180,180',
                'radius'         => 'required|numeric|min:1',
                'check_in_time'  => ['required', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
                'check_out_time' => ['required', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
            ]);

            $location = Location::where('location_id', $id)->firstOrFail();

            $in  = $this->normalizeTime($request->input('check_in_time'));
            $out = $this->normalizeTime($request->input('check_out_time'));

            if ($in && $out && !$this->isOutAfterIn($in, $out)) {
                return back()->withInput()->withErrors([
                    'check_out_time' => 'Jam check-out harus setelah jam check-in.'
                ]);
            }

            $location->office_name     = $validated['office_name'];
            $location->address         = $validated['address'];
            $location->city            = $validated['city'];
            $location->latitude        = $validated['latitude'];
            $location->longitude       = $validated['longitude'];
            $location->radius          = $validated['radius'];
            $location->check_in_time   = $in;
            $location->check_out_time  = $out;
            $location->save();

            return redirect()->route('admin.location')->with('success', 'Data kantor berhasil diperbarui.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data. Pastikan semua data diisi dengan benar.');
        }
    }

    public function destroy($id)
    {
        $location = Location::where('location_id', $id)->firstOrFail();

        $attendanceCount = Attendance::where('location_id', $location->location_id)->count();
        $linkCount       = OfficeLocationUser::where('location_id', $location->location_id)->count();

        if ($attendanceCount > 0 || $linkCount > 0) {
            $parts = [];
            if ($attendanceCount > 0) $parts[] = "{$attendanceCount} data kehadiran";
            if ($linkCount > 0)       $parts[] = "{$linkCount} relasi user lokasi";
            $reason = implode(' dan ', $parts);

            return redirect()->back()->with(
                'error',
                "Lokasi tidak bisa dihapus karena masih dipakai oleh {$reason}."
            );
        }

        try {
            $location->delete();
            return redirect()->back()->with('success', 'Lokasi dihapus, data kehadiran tetap aman.');
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                return redirect()->back()->with(
                    'error',
                    'Lokasi tidak bisa dihapus karena masih direferensikan data lain.'
                );
            }
            throw $e;
        }
    }


    private function normalizeTime(?string $value): ?string
    {
        if (!$value) return null;
        if (preg_match('/^\d{2}:\d{2}$/', $value)) {
            return $value . ':00';
        }
        if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $value)) {
            return $value;
        }
        return null;
    }


    private function isOutAfterIn(string $in, string $out): bool
    {
        [$ih, $im, $is] = array_map('intval', explode(':', $in));
        [$oh, $om, $os] = array_map('intval', explode(':', $out));
        $inSec  = $ih * 3600 + $im * 60 + $is;
        $outSec = $oh * 3600 + $om * 60 + $os;
        return $outSec > $inSec;
    }
}
