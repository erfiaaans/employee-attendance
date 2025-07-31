<form method="POST" action="{{ route('employee.profile.updateData', $user->user_id) }}">
    @csrf
    @isset($user)
        @method('PUT')
    @endisset

    <div class="row g-6 pt-3">
        <div class="col-md-6">
            <label for="Name" class="form-label">Name</label>
            <input class="form-control" type="text" id="name" name="name"
                value="{{ old('name', $user->name ?? '') }}" />
        </div>

        <div class="col-md-6">
            <label for="gender" class="form-label">Jenis Kelamin</label>
            @php
                use App\Enums\UserGender;
                $selectedGender = old('gender', $user->gender?->value ?? '');
            @endphp
            <select class="form-select" name="gender" id="gender">
                <option value="">Pilih Jenis Kelamin</option>
                @foreach (UserGender::cases() as $gender)
                    <option value="{{ $gender->value }}" {{ $selectedGender == $gender->value ? 'selected' : '' }}>
                        {{ $gender->value }}
                    </option>
                @endforeach
            </select>
            @error('gender')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-6">
            <label for="position" class="form-label">Jabatan</label>
            <input class="form-control" value="{{ old('position', $user->position ?? '') }}" type="text"
                id="position" name="position" />
        </div>
        <div class="col-md-6">
            <label for="email" class="form-label">E-mail</label>
            <input class="form-control" type="text" id="email" name="email"
                value="{{ old('email', $user->email ?? '') }}" />
        </div>
        <div class="col-md-6">
            <label class="form-label" for="telephone">Telephone</label>
            <div class="input-group input-group-merge">
                <input type="text" id="telephone" name="telephone" class="form-control"
                    value="{{ old('telephone', $user->telephone ?? '') }}" />
            </div>
        </div>
        {{-- <div class="col-md-6">
            <label for="office-name" class="form-label">Kantor</label>
            <input class="form-control" type="text" id="office-name" name="office-name"
                placeholder="Onmeso Madiun" />
        </div> --}}
        <div class="mt-4">
            <button type="submit" class="btn btn-primary me-3 mb-3"> Update</button>

        </div>
    </div>
</form>
