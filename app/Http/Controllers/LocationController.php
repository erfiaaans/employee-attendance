<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\OfficeLocationUser;
use Illuminate\Database\QueryException;

class LocationController extends Controller
{
    public function index(Request $request,  $id = null)
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
            $editLocation = Location::find($id);
        }

        return view('admin.location.index', compact('locations', 'editLocation'));
    }
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'office_name' => 'required|string|max:100',
                'address'     => 'required|string|max:255',
                'city'        => 'required|string|max:100',
                'latitude'    => 'required|numeric|between:-90,90',
                'longitude'   => 'required|numeric|between:-180,180',
                'radius'      => 'required|numeric|min:1',
            ]);

            $location = new Location();
            $location->location_id = uniqid();
            $location->office_name = $validated['office_name'];
            $location->address = $validated['address'];
            $location->city = $validated['city'];
            $location->latitude = $validated['latitude'];
            $location->longitude = $validated['longitude'];
            $location->radius = $validated['radius'];
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
                'office_name' => 'required|string|max:100',
                'address'     => 'required|string|max:255',
                'city'        => 'required|string|max:100',
                'latitude'    => 'required|numeric|between:-90,90',
                'longitude'   => 'required|numeric|between:-180,180',
                'radius'      => 'required|numeric|min:1',
            ]);
            $location = Location::findOrFail($id);
            $location->update($validated);
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
}
