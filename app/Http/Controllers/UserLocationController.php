<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Location;
use App\Models\OfficeLocationUser;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserLocationController extends Controller
{
    public function index(Request $request, $id = null)
    {
        $usersLocations =  OfficeLocationUser::with(['user', 'locations'])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('user', function ($qu) use ($search) {
                        $qu->where('name', 'like', "%{$search}%")
                            ->orWhere('position', 'like', "%{$search}%");
                    })
                        ->orWhereHas('locations', function ($ql) use ($search) {
                            $ql->where('office_name', 'like', "%{$search}%");
                        });
                });
            })
            // ->orderBy(User::select('name')->whereColumn('users.user_id', 'office_location_user.user_id'))
            ->join('users', 'users.user_id', '=', 'office_location_user.user_id')
            ->orderBy('users.name')
            ->select('office_location_user.*')
            ->paginate(10);

        $users = User::orderBy('name')->get();
        $locations = Location::orderBy('office_name')->get();

        return view('admin.userLocation.index', compact('usersLocations', 'users', 'locations'));
    }
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'user_id' => 'required|exists:users,user_id',
            'location_id' => 'required|exists:locations,location_id',
        ]);

        OfficeLocationUser::create([
            'location_user_id' => Str::uuid(),
            'user_id' => $request->user_id,
            'location_id' => $request->location_id,
            // 'created_by' => auth()->id(),
            // 'created_by' => auth()->user()->user_id,
        ]);
        return redirect()->route('admin.userLocation.index')->with('success', 'Lokasi pegawai berhasil ditambahkan.');
    }
    public function edit(Request $request, $id)
    {
        $usersLocations =  OfficeLocationUser::with(['user', 'locations'])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('user', function ($qu) use ($search) {
                        $qu->where('name', 'like', "%{$search}%")
                            ->orWhere('position', 'like', "%{$search}%");
                    })
                        ->orWhereHas('locations', function ($ql) use ($search) {
                            $ql->where('office_name', 'like', "%{$search}%");
                        });
                });
            })
            // ->orderBy(User::select('name')->whereColumn('users.user_id', 'office_location_user.user_id'))
            ->join('users', 'users.user_id', '=', 'office_location_user.user_id')
            ->orderBy('users.name')
            ->select('office_location_user.*')
            ->paginate(10);

        $officeLocationUser = OfficeLocationUser::findOrFail($id);
        $users = User::orderBy('name')->get();
        $locations = Location::orderBy('office_name')->get();

        return view('admin.userLocation.index', compact('officeLocationUser', 'users', 'locations', 'usersLocations'));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,user_id',
            'location_id' => 'required|exists:locations,location_id',
        ]);

        $officeLocationUser = OfficeLocationUser::findOrFail($id);
        $officeLocationUser->update([
            'user_id' => $request->user_id,
            'location_id' => $request->location_id,
        ]);

        return redirect()->route('admin.userLocation.index')->with('success', 'Data lokasi pegawai berhasil diperbarui.');
    }
    public function destroy($id)
    {
        $userLocation = OfficeLocationUser::findOrFail($id);
        // if ($user->profile_picture_url && \Storage::disk('public')->exists($userLocation->profile_picture_url)) {
        //     \Storage::disk('public')->delete($userLocation->profile_picture_url);
        // }
        $userLocation->delete();
        return redirect()->route('admin.userLocation.index')->with('success', 'User berhasil dihapus.');
    }
}
