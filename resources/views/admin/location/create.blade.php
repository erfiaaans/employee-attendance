<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">{{ isset($editLocation) ? 'Edit Kantor' : 'Tambah Kantor' }}</h5>
            </div>
            <div class="card-body">
                <form method="POST"
                    action="{{ isset($editLocation) ? route('admin.location.update', $editLocation->location_id) : route('admin.location.store') }}">
                    @csrf
                    @if (isset($editLocation))
                        @method('PUT')
                    @endif
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Nama Kantor</label>
                            <input type="text" name="office_name"
                                class="form-control @error('office_name') is-invalid @enderror"
                                value="{{ old('office_name', $editLocation->office_name ?? '') }}">
                            @error('office_name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Alamat</label>
                            <input type="text" name="address"
                                class="form-control @error('address') is-invalid @enderror"
                                value="{{ old('address', $editLocation->address ?? '') }}">
                            @error('address')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Kota</label>
                            <input type="text" name="city"
                                class="form-control @error('city') is-invalid @enderror"
                                value="{{ old('city', $editLocation->city ?? '') }}">
                            @error('city')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Latitude</label>
                            <input type="text" name="latitude"
                                class="form-control @error('latitude') is-invalid @enderror"
                                value="{{ old('latitude', $editLocation->latitude ?? '') }}">
                            @error('latitude')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Longitude</label>
                            <input type="text" name="longitude"
                                class="form-control @error('longitude') is-invalid @enderror"
                                value="{{ old('longitude', $editLocation->longitude ?? '') }}">
                            @error('longitude')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Radius (meter)</label>
                            <input type="number" name="radius"
                                class="form-control @error('radius') is-invalid @enderror"
                                value="{{ old('radius', $editLocation->radius ?? '') }}">
                            @error('radius')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Pilih Lokasi di Map</label>
                            <div id="map" style="height: 400px;"></div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-sm btn-primary">
                            {{ isset($editLocation) ? 'Update' : 'Simpan' }}
                        </button>
                        @if (isset($editLocation))
                            <a href="{{ route('admin.location') }}" class="btn btn-sm btn-secondary">Batal Edit</a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
