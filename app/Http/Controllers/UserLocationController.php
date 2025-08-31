<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Location;
use App\Models\OfficeLocationUser;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserLocationController extends Controller
{
    public function index(Request $request)
    {
        $users = User::where('role', 'employee')->get();

        $locations  = Location::orderBy('office_name')->get(['location_id', 'office_name']);
        $editItem = null;
        if ($request->filled('edit')) {
            $editId = $request->query('edit');
            $editItem = OfficeLocationUser::with(['user', 'locations'])
                ->where('location_user_id', $editId)
                ->first();

            if (!$editItem) {
                return redirect()->route('admin.userLocation.index')
                    ->with('error', 'Data yang ingin diedit tidak ditemukan.');
            }
        }

        return view('admin.userLocation.index', [
            'users'      => $users,
            'locations'  => $locations,
            'editItem'   => $editItem,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => ['required', 'exists:users,user_id'],
            'location_id' => [
                'required',
                'exists:locations,location_id',
                Rule::unique('office_location_user')->where(
                    fn($q) =>
                    $q->where('user_id', $request->user_id)
                        ->where('location_id', $request->location_id)
                ),
            ],
        ]);
        OfficeLocationUser::create([
            'location_user_id' => Str::uuid(),
            'user_id'    => $request->user_id,
            'location_id' => $request->location_id,
        ]);
        return redirect()->route('admin.userLocation.index')->with('success', 'Lokasi pegawai berhasil ditambahkan.');
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'user_id' => ['required', 'exists:users,user_id'],
            'location_id' => [
                'required',
                'exists:locations,location_id',
                Rule::unique('office_location_user')->ignore($id, 'location_user_id')->where(
                    fn($q) =>
                    $q->where('user_id', $request->user_id)
                        ->where('location_id', $request->location_id)
                ),
            ],
        ]);
        $officeLocationUser = OfficeLocationUser::where('location_user_id', $id)->firstOrFail();
        $officeLocationUser->update([
            'user_id'     => $request->user_id,
            'location_id' => $request->location_id,
        ]);
        return redirect()->route('admin.userLocation.index')->with('success', 'Data lokasi pegawai berhasil diperbarui.');
    }
    public function destroy($id)
    {
        $item = OfficeLocationUser::where('location_user_id', $id)->firstOrFail();
        $item->delete();
        return redirect()->route('admin.userLocation.index')->with('success', 'Data lokasi pegawai berhasil dihapus.');
    }
}
