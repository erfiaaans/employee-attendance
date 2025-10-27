<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Nama Pegawai</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $editUser->name ?? '') }}">
                    @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
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
                            <label class="form-check-label" for="role_{{ $role }}">{{ ucfirst($role) }}</label>
                        </div>
                    @endforeach
                    @error('role')
                        <div class="invalid-feedback d-block">
                            <strong>{{ $message }}</strong>
                        </div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Jabatan</label>
                    <input type="text" name="position" class="form-control @error('position') is-invalid @enderror"
                        value="{{ old('position', $editUser->position ?? '') }}">
                    @error('position')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Telepon</label>
                    <input type="text" name="telephone" class="form-control @error('telephone') is-invalid @enderror"
                        value="{{ old('telephone', $editUser->telephone ?? '') }}">
                    @error('telephone')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Foto</label>
                    <input type="url" name="profile_picture_url"
                        class="form-control @error('profile_picture_url') is-invalid @enderror"
                        value="{{ old('profile_picture_url', $editUser->profile_picture_url ?? '') }}">
                    @error('profile_picture_url')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label d-block">Jenis Kelamin</label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input @error('gender') is-invalid @enderror" type="radio"
                            name="gender" id="genderFemale" value="female"
                            {{ old('gender', isset($editUser) ? ($editUser->gender instanceof \App\Enums\UserGender ? $editUser->gender->value : $editUser->gender) : '') == 'female' ? 'checked' : '' }}>
                        <label class="form-check-label" for="genderFemale">Perempuan</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input @error('gender') is-invalid @enderror" type="radio"
                            name="gender" id="genderMale" value="male"
                            {{ old('gender', isset($editUser) ? ($editUser->gender instanceof \App\Enums\UserGender ? $editUser->gender->value : $editUser->gender) : '') == 'male' ? 'checked' : '' }}>
                        <label class="form-check-label" for="genderMale">Laki-laki</label>
                    </div>
                    @error('gender')
                        <div class="invalid-feedback d-block">
                            <strong>{{ $message }}</strong>
                        </div>
                    @enderror
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
