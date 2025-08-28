<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Enums\UserGender;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function profile()
    {
        $user = auth()->user();

        // if ($user->role == 'admin') {
        //     return view('admin.profile.index', compact('user'));
        // } elseif ($user->role == 'employee') {
        //     return view('employee.pofile.index', compact('user'));
        // }
        // abort(403, 'Unauthorized');
        return view('admin.profile.index', compact('user'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg, png|max:2048'
        ]);
        $path = $request->file('photo')->store('data/profile', 'public');
        $user = auth()->user();
        $user->profile_picture_url = $path;
        $user->save();

        return back()->with('success', 'Photo uploaded successfully!');
    }

    public function updatePhoto(Request $request, $id)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png|max:2048'
        ]);

        $user = User::findOrFail($id);

        try {
            if ($request->hasFile('photo')) {
                // Hapus foto lama jika ada
                if ($user->profile_picture_url && Storage::disk('public')->exists($user->profile_picture_url)) {
                    Storage::disk('public')->delete($user->profile_picture_url);
                }

                // Upload dan simpan foto baru
                $path = $request->file('photo')->store('profile', 'public');
                $user->profile_picture_url = $path;
                $user->save();
            }

            return redirect()->route('admin.profile.index')->with('success', 'Profile updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to upload profile photo. Please try again.');
        }
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        if ($user->profile_picture_url && Storage::disk('public')->exists($user->profile_picture_url)) {
            Storage::disk('public')->delete($user->profile_picture_url);
        }
        $user->profile_picture_url = null;
        $user->save();

        return redirect()->back()->with('success', 'Profile photo removed successfully!');
    }

    public function updateData(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:100',
                'position' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $id . ',user_id',
                'gender' => 'required|in:' . implode(',', UserGender::values()),
                'telephone' => 'nullable|string|max:15|regex:/^[0-9]+$/',
            ]);
            $user = User::findOrFail($id);
            $user->update($validated);
            return redirect()->route('admin.profile.index')->with('success', 'Data Profile berhasil diperbarui.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data. Pastikan semua data diisi dengan benar.');
        }
    }
}
