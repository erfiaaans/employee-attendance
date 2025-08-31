<?php

namespace App\Http\Controllers;

use App\Enums\UserGender;
use App\Enums\UserRole;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Location;
use App\Models\OfficeLocationUser;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class UserController extends Controller
{
    public function index(Request $request,  $id = null)
    {
        $users = User::orderBy('name', 'asc')->get();
        $editUser = null;
        if ($id) {
            $editUser = User::find($id);
        }
        return view('admin.user.index', compact('users', 'editUser'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => ['required', 'string', 'max:100'],
            'role'            => ['required', Rule::in(UserRole::values())],
            'position'        => ['required', 'string', 'max:255'],
            'email'           => ['required', 'email', 'max:255', 'unique:users,email'],
            'gender'          => ['required', Rule::in(UserGender::values())],
            'telephone'       => ['nullable', 'regex:/^\+?[0-9]{8,15}$/'],
            'password'        => ['required', 'string', 'min:6', 'confirmed'],
            'profile_picture' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user = new User();

        // kalau PK tidak auto-increment, generate UUID
        if (property_exists($user, 'incrementing') && $user->incrementing === false) {
            $user->user_id = (string) Str::uuid();
        }

        // isi field utama
        $user->name      = $validated['name'];
        $user->role      = $validated['role'];
        $user->position  = $validated['position'];
        $user->email     = $validated['email'];
        $user->gender    = $validated['gender'];
        $user->telephone = $validated['telephone'] ?? null;
        $user->password  = bcrypt($validated['password']);
        $user->save();

        // upload foto kalau ada
        $this->uploadProfilePicture($request, $user, 'profile_picture', 'profile');

        return redirect()->route('admin.user')->with('success', 'Data Pegawai berhasil ditambahkan.');
    }

    private function uploadProfilePicture(Request $request, User $user, string $input = 'profile_picture', string $dir = 'profiles'): void
    {
        if (!$request->hasFile($input)) {
            return;
        }

        // Hapus foto lama jika ada
        if ($user->profile_picture_url && Storage::disk('public')->exists($user->profile_picture_url)) {
            Storage::disk('public')->delete($user->profile_picture_url);
        }

        // Upload & simpan path relatif (tanpa /storage)
        $path = $request->file($input)->store($dir, 'public'); // contoh: profiles/abc.jpg
        $user->profile_picture_url = $path;                    // simpan path relatif di DB
        $user->save();
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'name'      => ['bail', 'required', 'string', 'max:100'],
            'role'      => ['bail', 'required', Rule::in(UserRole::values())],
            'position'  => ['bail', 'required', 'string', 'max:255'],
            'email'     => [
                'bail',
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($id, 'user_id'),
            ],
            'gender'    => ['bail', 'required', Rule::in(UserGender::values())],
            'telephone' => ['nullable', 'regex:/^\+?[0-9]{8,15}$/'],
            'password'  => ['nullable', 'string', 'min:6', 'confirmed'],
            'profile_picture'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_profile_picture' => ['nullable', 'boolean'],
        ];

        $messages = [
            'required'  => ':attribute wajib diisi.',
            'string'    => ':attribute harus berupa teks.',
            'max'       => ':attribute maksimal :max karakter.',
            'email'     => 'Format :attribute tidak valid.',
            'in'        => ':attribute tidak sesuai pilihan yang tersedia.',
            'unique'    => ':attribute sudah digunakan.',
            'regex'     => ':attribute harus berisi 8–15 digit (boleh diawali +).',
            'min'       => ':attribute minimal :min karakter.',
            'confirmed' => 'Konfirmasi :attribute tidak cocok.',
            'image'     => ':attribute harus berupa gambar.',
            'mimes'     => ':attribute harus berformat: :values.',
            'boolean'   => ':attribute tidak valid.',
        ];

        $attributes = [
            'name'                   => 'Nama',
            'role'                   => 'Peran',
            'position'               => 'Jabatan',
            'email'                  => 'Email',
            'gender'                 => 'Jenis kelamin',
            'telephone'              => 'Telepon',
            'password'               => 'Kata sandi',
            'password_confirmation'  => 'Konfirmasi kata sandi',
            'profile_picture'        => 'Foto profil',
            'remove_profile_picture' => 'Hapus foto',
        ];

        $validated = $request->validate($rules, $messages, $attributes);

        $user = User::findOrFail($id);

        // Hapus foto jika diminta
        if ($request->boolean('remove_profile_picture') && $user->profile_picture_url) {
            if (Storage::disk('public')->exists($user->profile_picture_url)) {
                Storage::disk('public')->delete($user->profile_picture_url);
            }
            $user->profile_picture_url = null;
            $user->save();
        }

        // Upload foto baru (jika ada)
        $this->uploadProfilePicture($request, $user, 'profile_picture', 'profile');

        // Handle password opsional
        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = bcrypt($validated['password']);
        }

        // Bersihkan field file dari mass assignment
        unset($validated['profile_picture'], $validated['remove_profile_picture']);

        $user->update($validated);

        return redirect()->route('admin.user')->with('success', 'Data Pegawai berhasil diperbarui.');
    }
    public function destroy($id)
    {
        $user = User::where('user_id', $id)->firstOrFail();

        $hasAttendance = $user->attendances()->exists();
        $hasOfficeLink = OfficeLocationUser::where('user_id', $user->user_id)->exists();

        if ($hasAttendance || $hasOfficeLink) {
            return redirect()->back()->with(
                'error',
                'Pegawai tidak bisa dihapus karena masih memiliki '
                    . ($hasAttendance ? 'data kehadiran ' : '')
                    . ($hasOfficeLink ? 'atau relasi lokasi kantor' : '')
                    . '.'
            );
        }

        try {
            // Hapus foto profil jika ada
            if ($user->profile_picture_url && Storage::disk('public')->exists($user->profile_picture_url)) {
                Storage::disk('public')->delete($user->profile_picture_url);
            }

            $user->delete();

            return redirect()->back()->with('success', 'Pegawai berhasil dihapus.');
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                return redirect()->back()->with(
                    'error',
                    'Pegawai tidak bisa dihapus karena masih direferensikan data lain.'
                );
            }
            throw $e;
        }
    }
}
