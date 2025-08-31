@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #map {
            height: 420px;
        }

        .object-fit-cover {
            object-fit: cover;
        }

        .ratio-16x9 {
            aspect-ratio: 16/9;
        }

        @media (max-width: 767.98px) {
            #submitBtn {
                position: sticky;
                bottom: 0;
                z-index: 2;
            }
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
            <div class="alert alert-info">Anda sudah melakukan Clock Out hari ini.</div>
        @else
            {{-- STATUS & ALERT --}}
            <div class="alert d-flex align-items-center justify-content-between mb-2 py-2 px-3 border">
                <div>
                    <strong class="me-2">Status:</strong>
                    <span id="radiusBadge" class="badge bg-secondary">Menunggu lokasi…</span>
                    <small id="radiusInfo" class="text-muted ms-2"></small>
                </div>
                <small class="text-muted d-none d-md-inline">Pastikan wajah terlihat jelas & lokasi aktif</small>
            </div>
            <div id="radiusAlert" class="alert alert-warning d-none mt-2 mb-3"></div>

            {{-- TABS (MOBILE) --}}
            <div class="d-md-none mb-2">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tabCamera" type="button"
                            role="tab">Kamera</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabMap" type="button"
                            role="tab">Peta</button>
                    </li>
                </ul>
            </div>

            <div class="row g-3">
                {{-- KAMERA + FORM (LEFT) --}}
                <div class="col-12 col-md-6 order-md-1">
                    <div class="tab-content d-md-block">
                        <div id="tabCamera" class="tab-pane fade show active d-md-block">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5 class="mb-0">Ambil Foto Clock Out</h5>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('employee.clock.clockout.store') }}" method="POST">
                                        @csrf

                                        {{-- Hidden inputs --}}
                                        <input type="hidden" id="location_id" name="location_id">
                                        <input type="hidden" id="clock_out_photo" name="clock_out_photo">
                                        <input type="hidden" id="clock_out_latitude" name="clock_out_latitude">
                                        <input type="hidden" id="clock_out_longitude" name="clock_out_longitude">
                                        <input type="hidden" id="outside_radius" name="outside_radius" value="0">
                                        <input type="hidden" id="distance_meters" name="distance_meters" value="">

                                        <div class="row g-2">
                                            <div class="col-12 col-lg-6">
                                                <label class="form-label">Nama Pegawai</label>
                                                <input type="text" class="form-control"
                                                    value="{{ auth()->user()->name }}" disabled>
                                            </div>
                                            <div class="col-12 col-lg-6">
                                                <label class="form-label">Jabatan</label>
                                                <input type="text" class="form-control"
                                                    value="{{ auth()->user()->position }}" disabled>
                                            </div>
                                        </div>

                                        {{-- Kamera / Preview --}}
                                        <div class="mt-3">
                                            <div id="cameraWrap" class="ratio ratio-16x9 rounded border">
                                                <video id="camera" autoplay playsinline
                                                    class="w-100 h-100 object-fit-cover rounded"></video>
                                            </div>
                                            <img id="photo_preview" src="#" alt="Preview"
                                                class="img-thumbnail mt-2 d-none" />
                                            <canvas id="snapshot" class="d-none"></canvas>

                                            <div class="d-flex gap-2 mt-2">
                                                <button type="button" class="btn btn-success"
                                                    onclick="takeSnapshot()">Ambil Foto</button>
                                                <button type="button" class="btn btn-outline-secondary d-none"
                                                    id="btnRetake" onclick="resetPhoto()">Ulangi Foto</button>
                                            </div>
                                        </div>

                                        {{-- Koordinat --}}
                                        <div class="mt-3">
                                            <label class="form-label">Koordinat</label>
                                            <div class="row g-2">
                                                <div class="col-6">
                                                    <input type="text" id="latitude" class="form-control"
                                                        placeholder="Latitude" disabled>
                                                </div>
                                                <div class="col-6">
                                                    <input type="text" id="longitude" class="form-control"
                                                        placeholder="Longitude" disabled>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Submit --}}
                                        <div class="mt-3">
                                            <button type="submit" class="btn btn-primary w-100 w-md-auto" id="submitBtn"
                                                disabled>Clock Out</button>
                                        </div>
                                    </form>
                                </div> {{-- /card-body --}}
                            </div> {{-- /card --}}
                        </div> {{-- /tabCamera --}}
                    </div>
                </div>

                {{-- MAP (RIGHT) --}}
                <div class="col-12 col-md-6 order-md-2">
                    <div class="tab-content d-md-block">
                        <div id="tabMap" class="tab-pane fade show d-md-block">
                            <div class="card h-100">
                                <div class="card-header d-flex align-items-center justify-content-between">
                                    <h5 class="mb-0">Lokasi di Peta</h5>
                                    <span class="badge bg-light text-dark">
                                        <span class="me-1">Terdekat:</span>
                                        <span id="nearestOfficeName">-</span>
                                    </span>
                                </div>
                                <div class="card-body p-0">
                                    <div id="map"
                                        style="width: 100%; border-top-left-radius: .5rem; border-top-right-radius: .5rem;">
                                    </div>
                                </div>
                            </div>
                        </div> {{-- /tabMap --}}
                    </div>
                </div>
            </div> {{-- /row --}}
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
        // ====== DATA LOKASI KANTOR ======
        const offices = @json($officePayload);

        // ====== ICONS ======
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

        // ====== MAP ======
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

        // ====== STATE ======
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
        const $badge = document.getElementById('radiusBadge');
        const $nearestName = document.getElementById('nearestOfficeName');

        const video = document.getElementById('camera');
        const canvas = document.getElementById('snapshot');
        const inputPhoto = document.getElementById('clock_out_photo');
        const previewImg = document.getElementById('photo_preview');
        const cameraWrap = document.getElementById('cameraWrap');
        const btnRetake = document.getElementById('btnRetake');

        function updateSubmitState() {
            // Submit aktif jika lokasi & foto tersedia
            $submit.disabled = !(hasGeolocation && hasPhoto);
        }

        function setStatus(inRadius, d, limit, name) {
            if (inRadius) {
                $badge.className = 'badge bg-success';
                $badge.textContent = 'Dalam radius';
            } else {
                $badge.className = 'badge bg-warning text-dark';
                $badge.textContent = 'Di luar radius';
            }
            if ($nearestName) $nearestName.textContent = name || '-';
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
                setStatus(false, 0, 0, '-');
                return;
            }

            const distances = offices
                .map(o => ({
                    ...o,
                    d: map.distance([lat, lng], [o.lat, o.lng])
                }))
                .sort((a, b) => a.d - b.d);

            const n = distances[0];
            $locId.value = n.id;
            $distH.value = Math.round(n.d);

            if (n.d <= n.radius) {
                $outside.value = 0;
                $hint.textContent = `Di dalam radius: ${n.name} (~${Math.round(n.d)} m, batas ${n.radius} m).`;
                $alert.classList.add('d-none');
                setStatus(true, n.d, n.radius, n.name);
            } else {
                $outside.value = 1;
                $hint.textContent = `Di luar radius. Terdekat: ${n.name} ~${Math.round(n.d)} m (batas ${n.radius} m).`;
                $alert.textContent = 'Anda berada di luar radius kantor atau lokasi perangkat tidak akurat.';
                $alert.classList.remove('d-none');
                setStatus(false, n.d, n.radius, n.name);
            }
        }

        // ====== GEOLOCATION ======
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                pos => setUserPosition(pos.coords.latitude, pos.coords.longitude),
                () => {
                    alert(
                        'Gagal mengambil lokasi. Anda tetap bisa coba Clock Out setelah ambil foto, namun lokasi tidak terekam.');
                    hasGeolocation = true; // tetap izinkan setelah foto
                    updateSubmitState();
                }, {
                    enableHighAccuracy: true,
                    timeout: 10000
                }
            );
        } else {
            alert('Geolocation tidak didukung browser ini.');
        }

        // ====== CAMERA ======
        navigator.mediaDevices.getUserMedia({
                video: true
            })
            .then(stream => {
                video.srcObject = stream;
            })
            .catch(err => {
                alert("Gagal mengakses kamera: " + err.message);
            });

        window.takeSnapshot = function takeSnapshot() {
            canvas.width = video.videoWidth || 640;
            canvas.height = video.videoHeight || 480;
            canvas.getContext('2d').drawImage(video, 0, 0);
            const imageData = canvas.toDataURL('image/png');
            inputPhoto.value = imageData;

            previewImg.src = imageData;
            previewImg.classList.remove('d-none');
            cameraWrap.classList.add('d-none');
            btnRetake.classList.remove('d-none');

            hasPhoto = true;
            updateSubmitState();
        }

        window.resetPhoto = function resetPhoto() {
            inputPhoto.value = '';
            previewImg.src = '#';
            previewImg.classList.add('d-none');
            cameraWrap.classList.remove('d-none');
            btnRetake.classList.add('d-none');

            hasPhoto = false;
            updateSubmitState();
        }
    </script>
@endsection
