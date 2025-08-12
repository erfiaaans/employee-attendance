@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #map {
            height: 400px
        }
    </style>
@endsection

@section('content')
    <div class="container mt-4">
        <h4 class="py-3 mb-4">
            <a href="#"><span class="text-muted fw-light">{{ __('Dashboards') }} /</span></a>
            <a href="#" class="text-secondary">{{ __('Clock In') }}</a>
        </h4>

        @if ($alreadyClockedIn)
            <div class="alert alert-info">Anda sudah melakukan Clock In hari ini.</div>
        @else
            <div class="card mb-3" style="max-width:1200px;">
                <div class="row g-0">
                    <div class="col-md-6 d-flex flex-column justify-content-center p-4">
                        <label class="form-label">Lokasi di Map</label>
                        <div id="map"></div>
                    </div>
                    <div class="col-md-6 p-4">
                        <form action="{{ route('employee.clock.clockin.store') }}" method="POST">
                            @csrf

                            {{-- akan diisi otomatis ketika user berada di salah satu lokasi --}}
                            <input type="hidden" id="location_id" name="location_id">

                            {{-- payload ke server --}}
                            <input type="hidden" id="clock_in_photo" name="clock_in_photo">
                            <input type="hidden" id="clock_in_latitude" name="clock_in_latitude">
                            <input type="hidden" id="clock_in_longitude" name="clock_in_longitude">

                            <div class="mb-3">
                                <label class="form-label">Nama Pegawai</label>
                                <input type="text" class="form-control" value="{{ auth()->user()->name }}" disabled>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Lokasi Saat Ini</label>
                                <input type="text" id="latitude" class="form-control mb-2" placeholder="Latitude"
                                    readonly>
                                <input type="text" id="longitude" class="form-control" placeholder="Longitude" readonly>
                                <div id="radiusInfo" class="form-text mt-1"></div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Ambil Foto Clock In</label>
                                <video id="camera" width="100%" height="auto" autoplay playsinline class="mb-2"
                                    style="border-radius:10px;border:1px solid #ccc;"></video>
                                <canvas id="snapshot" style="display:none;"></canvas>
                                <div class="mt-2 d-flex gap-2">
                                    <button type="button" class="btn btn-secondary mt-2" onclick="takeSnapshot()">Ambil
                                        Foto</button>
                                    <button type="button" class="btn btn-outline-danger mt-2 ms-2"
                                        onclick="resetPhoto()">Batal</button>
                                </div>
                                <img id="photo_preview" src="#" alt="Preview" class="img-thumbnail mt-2"
                                    style="display:none;" />
                            </div>

                            <button type="submit" class="btn btn-primary mt-3" id="submitBtn" disabled>Clock In</button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@section('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    @php
        // siapkan payload lokasi: id, name, lat, lng, radius
        $officePayload = $locations
            ->map(function ($l) {
                return [
                    'id' => $l->location_id,
                    'name' => $l->office_name ?? 'Lokasi',
                    'lat' => (float) $l->latitude,
                    'lng' => (float) $l->longitude,
                    'radius' => (int) ($l->radius ?? 50),
                ];
            })
            ->values();
    @endphp
    <script>
        // --- Data Lokasi dari server ---
        const offices = @json($officePayload);

        // --- Icons (merah untuk kantor, biru untuk user) ---
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

        // --- Map setup ---
        const map = L.map('map').setView([0, 0], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

        // Render semua lokasi kantor (marker merah + circle radius)
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

        // --- Geolocation user (marker biru) ---
        let userMarker = null;

        function setUserPosition(lat, lng) {
            if (userMarker) map.removeLayer(userMarker);
            userMarker = L.marker([lat, lng], {
                icon: BlueIcon
            }).addTo(map).bindPopup('Lokasi Anda').openPopup();

            // isi form
            document.getElementById('clock_in_latitude').value = lat;
            document.getElementById('clock_in_longitude').value = lng;
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;

            // ikutkan ke bounds supaya terlihat
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

            // validasi jarak ke SEMUA lokasi & pilih terdekat yang valid
            validateAgainstAllOffices(lat, lng);
        }

        // Hitung & validasi jarak terhadap semua lokasi
        function validateAgainstAllOffices(lat, lng) {
            const btn = document.getElementById('submitBtn');
            const hint = document.getElementById('radiusInfo');
            let within = [];
            offices.forEach(o => {
                const d = map.distance([lat, lng], [o.lat, o.lng]); // meter
                if (d <= o.radius) within.push({
                    id: o.id,
                    name: o.name,
                    dist: d,
                    radius: o.radius
                });
            });

            if (within.length) {
                // pilih yang paling dekat
                within.sort((a, b) => a.dist - b.dist);
                const chosen = within[0];
                document.getElementById('location_id').value = chosen.id;
                btn.disabled = false;
                hint.textContent =
                    `Di dalam radius: ${chosen.name} (~${Math.round(chosen.dist)} m, batas ${chosen.radius} m).`;
            } else {
                document.getElementById('location_id').value = '';
                btn.disabled = true;
                // tampilkan jarak terdekat sebagai info
                if (offices.length) {
                    const distances = offices.map(o => ({
                        name: o.name,
                        d: map.distance([lat, lng], [o.lat, o.lng]),
                        r: o.radius
                    }));
                    distances.sort((a, b) => a.d - b.d);
                    const nearest = distances[0];
                    hint.textContent =
                        `Di luar semua radius. Terdekat: ${nearest.name} ~${Math.round(nearest.d)} m (batas ${nearest.r} m).`;
                } else {
                    hint.textContent = 'Tidak ada lokasi terdaftar.';
                }
            }
        }

        // Ambil posisi user sekali (bisa diubah ke watchPosition kalau perlu realtime)
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                pos => setUserPosition(pos.coords.latitude, pos.coords.longitude),
                () => alert('Gagal mengambil lokasi.'), {
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
        const inputPhoto = document.getElementById('clock_in_photo');
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
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);
            const imageData = canvas.toDataURL('image/png');
            inputPhoto.value = imageData;
            previewImg.src = imageData;
            previewImg.style.display = 'block';
        }

        function resetPhoto() {
            inputPhoto.value = '';
            previewImg.src = '#';
            previewImg.style.display = 'none';
        }
        window.takeSnapshot = takeSnapshot;
        window.resetPhoto = resetPhoto;
    </script>
@endsection
