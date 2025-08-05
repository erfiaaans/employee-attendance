@extends('layouts.app')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4">
            <a href="#"><span class="text-muted fw-light">{{ __('Dashboards') }} /</span></a>
            <a href="#" class="text-secondary">{{ __('Edit Lokasi Pegawai') }}</a>
        </h4>

        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ __('Edit Lokasi Pegawai') }}</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.userLocation.update', $officeLocationUser->location_user_id) }}"
                            method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="user_id" class="form-label">Pilih Pegawai</label>
                                <select name="user_id" id="user_id" class="form-select" required>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->user_id }}"
                                            {{ $user->user_id == $officeLocationUser->user_id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="location_id" class="form-label">Pilih Kantor</label>
                                <select name="location_id" id="location_id" class="form-select" required>
                                    @foreach ($locations as $location)
                                        <option value="{{ $location->location_id }}"
                                            {{ $location->location_id == $officeLocationUser->location_id ? 'selected' : '' }}>
                                            {{ $location->office_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary">Update</button>
                            <a href="{{ route('admin.userLocation.index') }}" class="btn btn-secondary">Batal</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
