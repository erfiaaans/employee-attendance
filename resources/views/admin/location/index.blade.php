@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        .leaflet-control-lokasi-saya {
            padding: 6px;
            border-radius: 4px;
            box-shadow: 0 0 6px rgba(0, 0, 0, 0.2);
            cursor: pointer;
        }
    </style>
@endsection
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4">
            <a href="#"><span class="text-muted fw-light">{{ __('Dashboards') }} /</span></a>
            <a href="#" class="text-secondary">{{ __('Location') }}</a>
        </h4>
        @include('admin.location.create')
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ __('List Data Lokasi') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-md-5">
                                <form method="GET" action="{{ route('admin.location') }}" class="d-flex">
                                    <input type="text" name="search" class="form-control me-2 form-control"
                                        placeholder="Cari nama kantor, alamat, kota..." value="{{ request('search') }}">
                                    <button type="submit" class="btn btn-light btn-sm">Cari</button>
                                </form>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 table-responsive">
                                <table class="table table-bordered user_datatable table-sm" style="font-size: 90%">
                                    <thead class="table-light">
                                        <th style="width: 20px;">#</th>
                                        <th>Nama</th>
                                        <th>Alamat</th>
                                        <th>Kota</th>
                                        <th>Latitude</th>
                                        <th>Longitude</th>
                                        <th style="width: 50px;">Radius</th>
                                        <th style="width: 110px;">Aksi</th>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                        @forelse ($locations as $index => $location)
                                            <tr>
                                                <td>{{ $index + $locations->firstItem() }}</td>
                                                <td>{{ $location->office_name }}</td>
                                                <td>{{ $location->address }}</td>
                                                <td>{{ $location->city }}</td>
                                                <td>{{ $location->latitude }}</td>
                                                <td>{{ $location->longitude }}</td>
                                                <td>{{ $location->radius }}</td>
                                                <td class="text-center">
                                                    <div class="btn-group">
                                                        <form
                                                            action="{{ route('admin.location.destroy', $location->location_id) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <a href="{{ route('admin.location.edit', $location->location_id) }}"
                                                                class="btn btn-icon btn-primary btn-sm">
                                                                <span class="tf-icons bx bx-edit-alt text-white"></span>
                                                            </a>
                                                            <button type="submit"
                                                                class="btn btn-icon btn-danger btn-sm swalDeleteData"><i
                                                                    class="tf-icons bx bx-trash text-white"></i></button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center">Data tidak tersedia</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            {{-- Pagination --}}
                            <div class="my-4 px-3">
                                <nav aria-label="...">
                                    <ul class="pagination">
                                        <li class="page-item {{ $locations->onFirstPage() ? 'disabled' : '' }}">
                                            <a class="page-link"
                                                href="{{ $locations->previousPageUrl() ?? '#' }}">Previous</a>
                                        </li>
                                        @for ($i = 1; $i <= $locations->lastPage(); $i++)
                                            <li class="page-item {{ $i == $locations->currentPage() ? 'active' : '' }}">
                                                <a class="page-link"
                                                    href="{{ $locations->url($i) }}">{{ $i }}</a>
                                            </li>
                                        @endfor
                                        <li class="page-item {{ $locations->hasMorePages() ? '' : 'disabled' }}">
                                            <a class="page-link" href="{{ $locations->nextPageUrl() ?? '#' }}">Next</a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        var defaultLat = {{ old('latitude', $editLocation->latitude ?? -7.250445) }};
        var defaultLng = {{ old('longitude', $editLocation->longitude ?? 112.768845) }};
        var defaultRadius = {{ old('radius', $editLocation->radius ?? 50) }};
        var map = L.map('map').setView([defaultLat, defaultLng], 18);
        map.attributionControl.setPrefix("");
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap',
        }).addTo(map);
        var marker = L.marker([defaultLat, defaultLng], {
            draggable: true
        }).addTo(map);
        var circle = L.circle([defaultLat, defaultLng], {
            radius: defaultRadius
        }).addTo(map);
        marker.on('drag', function(e) {
            var latlng = e.latlng;
            document.querySelector('input[name="latitude"]').value = latlng.lat;
            document.querySelector('input[name="longitude"]').value = latlng.lng;
            circle.setLatLng(latlng);
        });
        document.querySelector('input[name="radius"]').addEventListener('input', function(e) {
            circle.setRadius(e.target.value);
        });
        L.Control.LokasiSaya = L.Control.extend({
            options: {
                position: 'topleft'
            },
            onAdd: function(map) {
                var btn = L.DomUtil.create('button', 'leaflet-control-lokasi-saya btn btn-sm btn-primary');
                btn.innerHTML = '📍 Lokasi Saya';
                btn.style.cursor = 'pointer';
                btn.style.margin = '5px';
                L.DomEvent.disableClickPropagation(btn);
                L.DomEvent.on(btn, 'click', function(e) {
                    e.preventDefault();
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(function(position) {
                            var lat = position.coords.latitude;
                            var lng = position.coords.longitude;
                            marker.setLatLng([lat, lng]);
                            circle.setLatLng([lat, lng]);
                            document.querySelector('input[name="latitude"]').value = lat;
                            document.querySelector('input[name="longitude"]').value = lng;
                            map.setView([lat, lng], 18);
                            marker.bindPopup("Lokasi Anda").openPopup();
                        }, function(err) {
                            alert("Gagal mendapatkan lokasi: " + err.message);
                        });
                    } else {
                        alert("Browser tidak mendukung geolokasi.");
                    }
                });
                return btn;
            },
            onRemove: function(map) {}
        });
        L.control.lokasiSaya = function(opts) {
            return new L.Control.LokasiSaya(opts || {});
        };
        L.control.lokasiSaya().addTo(map);
    </script>
@endsection
