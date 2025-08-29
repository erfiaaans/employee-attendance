@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #map {
            height: 400px;
        }
    </style>
@endsection

@section('content')
    <div class="container mt-4">
        <h4 class="py-3 mb-4">
            <a href="#"><span class="text-muted fw-light">{{ __('Dashboards') }} /</span></a>
            <a href="#" class="text-secondary">{{ __('Clock Out') }}</a>
        </h4>

        @if ($alreadyClockedOut)
            <div class="alert alert-info">
                Anda sudah melakukan Clock Out hari ini.
            </div>
        @else
            <div id="radiusAlert" class="alert alert-warning d-none"></div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3 h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">{{ 'Lokasi di Maps' }}</h5>
                        </div>
                        <div id="map" style="height: 400px; width: 100%;"></div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card mb-3 h-100">
                        <div class="card-body p-4">
                            <form action="{{ route('employee.clock.clockout.store') }}" method="POST">
                                @csrf

                                <input type="hidden" id="location_id" name="location_id">
                                <input type="hidden" id="clock_out_photo" name="clock_out_photo">
                                <input type="hidden" id="clock_out_latitude" name="clock_out_latitude">
                                <input type="hidden" id="clock_out_longitude" name="clock_out_longitude">
                                <input type="hidden" id="outside_radius" name="outside_radius" value="0">
                                <input type="hidden" id="distance_meters" name="distance_meters" value="">

                                <div class="mb-3">
                                    <label class="form-label">Nama Pegawai</label>
                                    <input type="text" class="form-control" value="{{ auth()->user()->name }}" disabled>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Jabatan</label>
                                    <input type="text" class="form-control" value="{{ auth()->user()->position }}"
                                        disabled>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Lokasi Saat Ini</label>
                                    <input type="text" id="latitude" class="form-control mb-2" placeholder="Latitude"
                                        disabled>
                                    <input type="text" id="longitude" class="form-control" placeholder="Longitude"
                                        disabled>
                                    <div id="radiusInfo" class="form-text mt-1"></div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Ambil Foto Clock Out</label>
                                    <video id="camera" width="100%" height="auto" autoplay playsinline class="mb-2"
                                        style="border-radius: 10px; border: 1px solid #ccc;"></video>
                                    <canvas id="snapshot" style="display:none;"></canvas>
                                    <div class="mt-2 d-flex gap-2">
                                        <button type="button" class="btn btn-success mt-2" onclick="takeSnapshot()">Ambil
                                            Foto</button>
                                        <button type="button" class="btn btn-outline-danger mt-2 ms-2"
                                            onclick="resetPhoto()">Batal</button>
                                    </div>
                                    <img id="photo_preview" src="#" alt="Preview" class="img-thumbnail mt-2"
                                        style="display:none;" />
                                </div>

                                <button type="submit" class="btn btn-primary mt-3" id="submitBtn" disabled>Clock
                                    Out</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@section('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    @php
        $officePayload = $locations
            ->map(function ($l) {
                return [
                    'id' => $l->location_id,
                    'name' => $l->office_name ?? 'Lokasi',
                    'lat' => (float) $l->latitude,
                    'lng' => (float) $l->longitude,
                    'radius' => (int) ($l->radius ?? ($l->radius_meters ?? 50)),
                ];
            })
            ->values();
    @endphp
    <script>
        const offices = @json($officePayload);

        const RedIcon = new L.Icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
            shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });
        const BlueIcon = new L.Icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-blue.png',
            shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });

        const map = L.map('map').setView([0, 0], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

        const bounds = [];
        const officeLayers = [];
        offices.forEach(o => {
            const m = L.marker([o.lat, o.lng], {
                icon: RedIcon
            }).addTo(map).bindPopup(`${o.name}`);
            const c = L.circle([o.lat, o.lng], {
                radius: o.radius,
                color: 'red',
                fillColor: '#f003',
                fillOpacity: 0.2
            }).addTo(map);
            officeLayers.push({
                marker: m,
                circle: c,
                data: o
            });
            bounds.push([o.lat, o.lng]);
        });
        if (bounds.length) map.fitBounds(bounds, {
            padding: [30, 30]
        });

        let userMarker = null;
        let hasGeolocation = false;
        let hasPhoto = false;

        const $latH = document.getElementById('clock_out_latitude');
        const $lngH = document.getElementById('clock_out_longitude');
        const $latT = document.getElementById('latitude');
        const $lngT = document.getElementById('longitude');
        const $locId = document.getElementById('location_id');
        const $outside = document.getElementById('outside_radius');
        const $distH = document.getElementById('distance_meters');
        const $hint = document.getElementById('radiusInfo');
        const $alert = document.getElementById('radiusAlert');
        const $submit = document.getElementById('submitBtn');

        function updateSubmitState() {
            // Submit aktif kalau sudah punya lokasi & foto
            $submit.disabled = !(hasGeolocation && hasPhoto);
        }

        function setUserPosition(lat, lng) {
            if (userMarker) map.removeLayer(userMarker);
            userMarker = L.marker([lat, lng], {
                icon: BlueIcon
            }).addTo(map).bindPopup('Lokasi Anda').openPopup();

            $latH.value = lat;
            $lngH.value = lng;
            $latT.value = lat;
            $lngT.value = lng;
            hasGeolocation = true;
            updateSubmitState();

            if (bounds.length) {
                const all = bounds.concat([
                    [lat, lng]
                ]);
                map.fitBounds(all, {
                    padding: [30, 30]
                });
            } else {
                map.setView([lat, lng], 16);
            }
            evaluateAgainstOffices(lat, lng);
        }

        function evaluateAgainstOffices(lat, lng) {
            if (!offices.length) {
                $locId.value = '';
                $outside.value = 0;
                $distH.value = '';
                $hint.textContent = 'Tidak ada lokasi terdaftar.';
                $alert.classList.add('d-none');
                return;
            }

            // Hitung jarak ke semua kantor
            const distances = offices.map(o => {
                const d = map.distance([lat, lng], [o.lat, o.lng]); // meter
                return {
                    id: o.id,
                    name: o.name,
                    radius: o.radius,
                    d
                };
            }).sort((a, b) => a.d - b.d);

            const nearest = distances[0];
            $locId.value = nearest.id; // tetap isi lokasi terdekat
            $distH.value = Math.round(nearest.d);

            if (nearest.d <= nearest.radius) {
                // Di dalam radius
                $outside.value = 0;
                $hint.textContent =
                    `Di dalam radius: ${nearest.name} (~${Math.round(nearest.d)} m, batas ${nearest.radius} m).`;
                $alert.classList.add('d-none');
            } else {
                // Di luar radius → tetap boleh clock out, tapi beri peringatan
                $outside.value = 1;
                $hint.textContent =
                    `Di luar radius. Terdekat: ${nearest.name} ~${Math.round(nearest.d)} m (batas ${nearest.radius} m).`;
                $alert.textContent =
                    'Anda berada di luar radius kantor atau lokasi perangkat tidak akurat.';
                $alert.classList.remove('d-none');
            }
        }

        // Geolocation
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                pos => setUserPosition(pos.coords.latitude, pos.coords.longitude),
                () => {
                    alert(
                        'Gagal mengambil lokasi. Anda tetap bisa coba Clock Out setelah ambil foto, namun lokasi tidak terekam.'
                    );
                    hasGeolocation = true; // izinkan setelah foto bila user tetap lanjut
                    updateSubmitState();
                }, {
                    enableHighAccuracy: true,
                    timeout: 10000
                }
            );
        } else {
            alert('Geolocation tidak didukung browser ini.');
        }

        // Kamera
        const video = document.getElementById('camera');
        const canvas = document.getElementById('snapshot');
        const inputPhoto = document.getElementById('clock_out_photo');
        const previewImg = document.getElementById('photo_preview');

        navigator.mediaDevices.getUserMedia({
                video: true
            })
            .then(stream => {
                video.srcObject = stream;
            })
            .catch(err => {
                alert("Gagal mengakses kamera: " + err.message);
            });

        function takeSnapshot() {
            canvas.width = video.videoWidth || 640;
            canvas.height = video.videoHeight || 480;
            canvas.getContext('2d').drawImage(video, 0, 0);
            const imageData = canvas.toDataURL('image/png');
            inputPhoto.value = imageData;
            previewImg.src = imageData;
            previewImg.style.display = 'block';
            hasPhoto = true;
            updateSubmitState();
        }

        function resetPhoto() {
            inputPhoto.value = '';
            previewImg.src = '#';
            previewImg.style.display = 'none';
            hasPhoto = false;
            updateSubmitState();
        }

        window.takeSnapshot = takeSnapshot;
        window.resetPhoto = resetPhoto;
    </script>
@endsection
