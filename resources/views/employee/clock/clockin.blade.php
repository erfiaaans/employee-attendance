@extends('layouts.app')
@section('content')
    <div class="container mt-4">
        <h4 class="py-3 mb-4">
            <a href="#"><span class="text-muted fw-light">{{ __('Dashboards') }} /</span>
            </a>
            <a href="#" class="text-secondary">
                {{ __('Clock In') }}
        </h4>
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <div class="card mb-3" style="max-width: 1200px;">
            <div class="row g-0">
                <div class="col-md-6 d-flex align-items-center justify-content-center p-4">
                    <img src="{{ asset('img/icons/clock_in.png') }}" class="img-fluid rounded-start" alt="...">
                </div>
                <div class="col-md-6 p-4">
                    <form action="{{ route('employee.clock.clockin.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
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
                            <canvas id="snapshot" style="display: none;"></canvas>
                            <input type="hidden" name="clock_in_photo" id="clock_in_photo">
                            <button type="button" class="btn btn-secondary mt-2" onclick="takeSnapshot()">Ambil
                                Foto</button>
                            <img id="photo_preview" src="#" alt="Preview" class="img-thumbnail mt-2"
                                style="display: none;" />
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    document.getElementById('latitude').value = position.coords.latitude;
                    document.getElementById('longitude').value = position.coords.longitude;
                }, function(error) {
                    alert('Gagal mengambil lokasi. Aktifkan GPS Anda.');
                });
            } else {
                alert('Geolocation tidak didukung oleh browser ini.');
            }
            const video = document.getElementById('camera');
            navigator.mediaDevices.getUserMedia({
                    video: true
                })
                .then(stream => {
                    video.srcObject = stream;
                })
                .catch(err => {
                    console.error('Kamera error:', err);
                    alert('Kamera tidak bisa diakses: ' + err.message);
                });
        });

        function takeSnapshot() {
            const video = document.getElementById('camera');
            const canvas = document.getElementById('snapshot');
            const inputPhoto = document.getElementById('clock_in_photo');
            const preview = document.getElementById('photo_preview');

            const context = canvas.getContext('2d');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;

            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            const imageData = canvas.toDataURL('image/png');
            inputPhoto.value = imageData;

            // Tampilkan preview
            preview.src = imageData;
            preview.style.display = 'block';

            alert('Foto berhasil diambil!');
        }
    </script>
@endsection
