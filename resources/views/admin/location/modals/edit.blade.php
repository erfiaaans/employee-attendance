@foreach ($locations as $location)
    <!-- Modal Edit per Location -->
    <div class="modal fade" id="editKantorModal{{ $location->id }}" tabindex="-1"
        aria-labelledby="editKantorLabel{{ $location->id }}" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('locations.update', $location->id) }}" method="POST" class="modal-content">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title" id="editKantorLabel{{ $location->id }}">Edit Kantor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">Nama Kantor</label>
                        <input type="text" name="office_name" class="form-control"
                            value="{{ $location->office_name }}" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Alamat</label>
                        <input type="text" name="address" class="form-control" value="{{ $location->address }}"
                            required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Kota</label>
                        <input type="text" name="city" class="form-control" value="{{ $location->city }}"
                            required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Latitude</label>
                        <input type="text" name="latitude" class="form-control" value="{{ $location->latitude }}"
                            required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Longitude</label>
                        <input type="text" name="longitude" class="form-control" value="{{ $location->longitude }}"
                            required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Radius (meter)</label>
                        <input type="number" name="radius" class="form-control" value="{{ $location->radius }}"
                            required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-sm">Simpan Perubahan</button>
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>
@endforeach
