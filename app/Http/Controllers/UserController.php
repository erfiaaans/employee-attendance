<?php

namespace App\Http\Controllers;

use App\Enums\UserGender;
use App\Enums\UserRole;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Location;

class UserController extends Controller
{
    public function index(Request $request,  $id = null)
    {
        $search = $request->input('search');
        $users = User::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%")
                ->orWhere('position', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        })
            ->orderBy('name')
            ->paginate(10)
            ->appends(['search' => $search]);

        $allUsers = User::all();
        $editUser = null;
        if ($id) {
            $editUser = User::find($id);
        }
        return view('admin.user.index', compact('users', 'editUser', 'allUsers'));
    }
    public function profile()
    {
        $user = auth()->user();
        return view('admin.profile.index', compact('user'));
    }
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:100',
                'role' => 'required|in:' . implode(',', UserRole::values()),
                'position'     => 'required|string|max:255',
                // 'office_name' => 'required|string|max:100',
                'email' => 'required|email|unique:users,email',
                'gender'    => 'required|in:' . implode(',', UserGender::values()),
                'profile_picture_url' => 'nullable|url|max:255',
                'telephone' => 'nullable|string|max:15|regex:/^[0-9]+$/',
                'password' => 'required'
            ]);

            $user = new user();
            $user->user_id = uniqid();
            $user->name = $validated['name'];
            $user->position = $validated['position'];
            $user->role = $validated['role'];
            // $user->office_name = $validated['office_name'];
            $user->email = $validated['email'];
            $user->gender = $validated['gender'];
            $user->profile_picture_url = $validated['profile_picture_url'];
            $user->telephone = $validated['telephone'];
            $user->password = $validated['password'];
            $user->save();
            return redirect()->back()->with('success', 'Pegawai berhasil ditambahkan!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            // dd($e);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data. Pastikan semua data diisi dengan benar.');
        }
    }
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:100',
                'role' => 'required|in:' . implode(',', UserRole::values()),
                'position' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $id . ',user_id',
                'gender' => 'required|in:' . implode(',', UserGender::values()),
                'profile_picture_url' => 'nullable|url|max:255',
                'telephone' => 'nullable|string|max:15|regex:/^[0-9]+$/',
                'password' => 'nullable|string|min:6', // Tidak wajib
            ]);

            $user = User::findOrFail($id);

            // Jika password tidak dikirim, hapus dari array agar tidak diupdate
            if (empty($validated['password'])) {
                unset($validated['password']);
            } else {
                // Enkripsi password baru
                $validated['password'] = bcrypt($validated['password']);
            }

            $user->update($validated);

            return redirect()->route('admin.user')->with('success', 'Data Pegawai berhasil diperbarui.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data. Pastikan semua data diisi dengan benar.');
        }
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->back()->with('success', 'Pegawai berhasil dihapus.');
    }

    public function create()
    {
        $users = User::all();
        return view('admin.user.create', compact('users'));
    }
}
