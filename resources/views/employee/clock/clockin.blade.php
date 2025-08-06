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
            <a href="#"><span class="text-muted fw-light">{{ __('Dashboards') }} /</span>
            </a>
            <a href="#" class="text-secondary">
                {{ __('Clock In') }}
        </h4>
        @if ($alreadyClockedIn)
            <div class="alert alert-info">
                Anda sudah melakukan Clock In hari ini.
            </div>
        @else
            <div class="card mb-3" style="max-width: 1200px;">
                <div class="row g-0">
                    <div class="col-md-6 d-flex flex-column justify-content-center p-4">
                        <label class="form-label">Lokasi di Map</label>
                        <div id="map" style="height: 400px;"></div>
                    </div>
                    <div class="col-md-6 p-4">
                        <form action="{{ route('employee.clock.clockin.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="location_id" value="{{ $location->location_id }}">
                            <input type="hidden" id="clock_in_photo" name="clock_in_photo">
                            <input type="hidden" id="clock_in_latitude" name="clock_in_latitude">
                            <input type="hidden" id="clock_in_longitude" name="clock_in_longitude">
                            <div class="mb-3">
                                <label class="form-label">Nama Pegawai</label>
                                <input type="text" class="form-control" value="{{ auth()->user()->name }}" disabled>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Lokasi Saat Ini</label>
                                <input type="text" id="latitude" name="clock_in_latitude" class="form-control mb-2"
                                    placeholder="Latitude" readonly required>
                                <input type="text" id="longitude" name="clock_in_longitude" class="form-control"
                                    placeholder="Longitude" readonly required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Ambil Foto Clock In</label>
                                <video id="camera" width="100%" height="auto" autoplay playsinline class="mb-2"
                                    style="border-radius: 10px; border: 1px solid #ccc;"></video>
                                <input type="hidden" name="clock_in_photo_url" id="clock_in_photo_url">
                                <canvas id="snapshot" style="display: none;"></canvas>
                                <div class="mt-2 d-flex gap-2">
                                    <button type="button" class="btn btn-secondary mt-2" onclick="takeSnapshot()">Ambil
                                        Foto</button>
                                    <button type="button" class="btn btn-outline-danger mt-2 ms-2"
                                        onclick="resetPhoto()">Batal</button>
                                </div>
                                <img id="photo_preview" src="#" alt="Preview" class="img-thumbnail mt-2"
                                    style="display: none;" />
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
    <script>
        const officeLat = {{ $location->latitude }};
        const officeLng = {{ $location->longitude }};
        const radiusMaksimal = 50;
        const map = L.map('map').setView([officeLat, officeLng], 16);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
        L.marker([officeLat, officeLng]).addTo(map).bindPopup('Lokasi Kantor');
        const circle = L.circle([officeLat, officeLng], {
            radius: radiusMaksimal,
            color: 'green',
            fillColor: '#0f03',
            fillOpacity: 0.2
        }).addTo(map);
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(position => {
                const userLat = position.coords.latitude;
                const userLng = position.coords.longitude;

                document.getElementById('clock_in_latitude').value = userLat;
                document.getElementById('clock_in_longitude').value = userLng;
                document.getElementById('latitude').value = userLat;
                document.getElementById('longitude').value = userLng;

                L.marker([userLat, userLng]).addTo(map).bindPopup('Lokasi Anda').openPopup();
                map.setView([userLat, userLng], 16);

                const distance = map.distance([userLat, userLng], [officeLat, officeLng]);

                if (distance <= radiusMaksimal) {
                    document.getElementById('submitBtn').disabled = false;
                } else {
                    alert("Anda berada di luar radius kantor (" + Math.round(distance) + " meter).");
                }
            }, () => {
                alert('Gagal mengambil lokasi.');
            });
        }
        const video = document.getElementById('camera');
        const canvas = document.getElementById('snapshot');
        const inputPhoto = document.getElementById('clock_in_photo');
        // inputPhoto.value = imageData;
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
    </script>
@endsection
