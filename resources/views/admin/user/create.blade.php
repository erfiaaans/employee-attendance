<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">{{ isset($editUser) ? 'Edit Pegawai' : 'Tambah Pegawai' }}</h5>
            </div>
            <div class="card-body">
                <form method="POST"
                    action="{{ isset($editUser) ? route('admin.user.update', $editUser->user_id) : route('admin.user.store') }}"
                    enctype="multipart/form-data">
                    @csrf
                    @if (isset($editUser))
                        @method('PUT')
                    @endif
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Nama Pegawai</label>
                            <input type="text" name="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $editUser->name ?? '') }}">
                            @error('name')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label d-block">Role</label>
                            @php
                                use App\Enums\UserRole;
                                $roles = UserRole::values();
                                $selectedRole = old(
                                    'role',
                                    isset($editUser)
                                        ? ($editUser->role instanceof UserRole
                                            ? $editUser->role->value
                                            : $editUser->role)
                                        : '',
                                );
                            @endphp
                            @foreach ($roles as $role)
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input @error('role') is-invalid @enderror" type="radio"
                                        name="role" id="role_{{ $role }}" value="{{ $role }}"
                                        {{ $selectedRole === $role ? 'checked' : '' }}>
                                    <label class="form-check-label"
                                        for="role_{{ $role }}">{{ ucfirst($role) }}</label>
                                </div>
                            @endforeach
                            @error('role')
                                <div class="invalid-feedback d-block"><strong>{{ $message }}</strong></div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Jabatan</label>
                            <input type="text" name="position"
                                class="form-control @error('position') is-invalid @enderror"
                                value="{{ old('position', $editUser->position ?? '') }}">
                            @error('position')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Email</label>
                            <input type="email" name="email"
                                class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email', $editUser->email ?? '') }}">
                            @error('email')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            @php
                                use App\Enums\UserGender;
                                $selectedGender = old(
                                    'gender',
                                    isset($editUser)
                                        ? ($editUser->gender instanceof UserGender
                                            ? $editUser->gender->value
                                            : $editUser->gender)
                                        : '',
                                );
                            @endphp
                            <label class="form-label d-block">Jenis Kelamin</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input @error('gender') is-invalid @enderror" type="radio"
                                    name="gender" id="genderFemale" value="{{ UserGender::Female->value }}"
                                    {{ $selectedGender === UserGender::Female->value ? 'checked' : '' }}>
                                <label class="form-check-label"
                                    for="genderFemale">{{ UserGender::Female->value }}</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input @error('gender') is-invalid @enderror" type="radio"
                                    name="gender" id="genderMale" value="{{ UserGender::Male->value }}"
                                    {{ $selectedGender === UserGender::Male->value ? 'checked' : '' }}>
                                <label class="form-check-label" for="genderMale">{{ UserGender::Male->value }}</label>
                            </div>
                            @error('gender')
                                <div class="invalid-feedback d-block"><strong>{{ $message }}</strong></div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">
                                Password {!! isset($editUser) ? '<small class="text-muted">(kosongkan jika tidak ganti)</small>' : '' !!}
                            </label>
                            <input type="password" name="password"
                                class="form-control @error('password') is-invalid @enderror"
                                autocomplete="new-password">
                            @error('password')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Telepon</label>
                            <input type="text" name="telephone"
                                class="form-control @error('telephone') is-invalid @enderror"
                                value="{{ old('telephone', $editUser->telephone ?? '') }}" placeholder="0812xxxxxx">
                            @error('telephone')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Foto</label>
                            <input type="file" name="profile_picture" accept="image/*"
                                class="form-control @error('profile_picture') is-invalid @enderror">
                            @error('profile_picture')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                            @if (!empty($editUser?->profile_picture_url))
                                <div class="mt-2">
                                    <img src="{{ $editUser->photo_url }}" alt="Foto" width="120"
                                        class="rounded">
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" name="remove_profile_picture"
                                            id="remove_profile_picture" value="1">
                                        <label class="form-check-label" for="remove_profile_picture">Hapus
                                            foto</label>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control"
                                autocomplete="new-password">
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-sm btn-primary">
                            {{ isset($editUser) ? 'Update' : 'Simpan' }}
                        </button>
                        @if (isset($editUser))
                            <a href="{{ route('admin.user') }}" class="btn btn-sm btn-secondary">Batal Edit</a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
