<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Location;
use App\Models\OfficeLocationUser;
use Illuminate\Support\Facades\Storage;

class UserLocationController extends Controller
{
    public function index(Request $request, $id = null)
    {
        $usersLocations =  OfficeLocationUser::with(['user', 'locations'])
            ->when($request->search, function ($query, $search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                    ->orWhere('position', 'like', "%{$search}%")
                    ->orWhereHas('locations', function ($q) use ($search) {
                        $q->where('office_name', 'like', "%{$search}%");
                    });
            })
            ->orderBy(User::select('name')->whereColumn('users.user_id', 'office_location_user.user_id'))
            ->paginate(10);

        return view('admin.userLocation.index', compact('usersLocations'));
    }
    public function store(Request $request) {}
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $locations = Location::all();

        return view('admin.userLocation.edit', compact('user', 'locations'));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'location_id' => 'required|exists: locations, location_id',
        ]);
        $user = User::findOrFail($id);
        $user->location_id = $request->location_id;
        $user->save();

        return redirect()->route('admin.userLocation.index')->with('success', 'Lokasi user berhasil diperbarui.');
    }
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        if ($user->profile_picture_url && \Storage::disk('public')->exists($user->profile_picture_url)) {
            \Storage::disk('public')->delete($user->profile_picture_url);
        }
        $user->delete();
        return redirect()->route('admin.userLocation')->with('success', 'User berhasil dihapus.');
    }
}
