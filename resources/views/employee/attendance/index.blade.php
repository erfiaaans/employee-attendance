@extends('layouts.app')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4">
            <a href="#"><span class="text-muted fw-light">{{ __('Dashboards') }} /</span></a>
            <a href="#" class="text-secondary">{{ __('Riwayat Absensi') }}</a>
        </h4>

        <div class="row">
            <div class="card md-12">
                <div class="col-mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ 'Riwayat Absensi - ' . Auth::user()->name }}</h5>
                    </div>

                    <div class="card-body">
                        <form method="GET" action="{{ route('employee.attendance.index') }}">
                            <div class="row mb-2">
                                <div class="col-md-12">
                                    <div class="d-flex align-items-center flex-wrap gap-2">
                                        <input type="text" name="search" class="form-control me-2"
                                            style="max-width: 250px;" placeholder="Cari tanggal atau jam masuk..."
                                            value="{{ request('search') }}">
                                        @if (request('filter'))
                                            <input type="hidden" name="filter" value="{{ request('filter') }}">
                                        @endif
                                        <button type="submit" class="btn btn-light btn-sm">Cari</button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Detail Masuk</th>
                                        <th>Detail Keluar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($attendances as $date => $items)
                                        @php
                                            $clockIn = $items
                                                ->whereNotNull('clock_in_time')
                                                ->sortBy('clock_in_time')
                                                ->first();
                                            $clockOut = $items
                                                ->whereNotNull('clock_out_time')
                                                ->sortByDesc('clock_out_time')
                                                ->first();

                                            $inTimeObj = $clockIn?->clock_in_time
                                                ? \Carbon\Carbon::parse($clockIn->clock_in_time)
                                                : null;
                                            $outTimeObj = $clockOut?->clock_out_time
                                                ? \Carbon\Carbon::parse($clockOut->clock_out_time)
                                                : null;

                                            $inTime = $inTimeObj ? $inTimeObj->format('H:i') : '-';
                                            $outTime = $outTimeObj ? $outTimeObj->format('H:i') : '-';

                                            $inLat = $clockIn?->clock_in_latitude;
                                            $inLng = $clockIn?->clock_in_longitude;
                                            $outLat = $clockOut?->clock_out_latitude;
                                            $outLng = $clockOut?->clock_out_longitude;

                                            $radiusMeter =
                                                (float) ($clockIn?->location->radius_meter ??
                                                    ($clockOut?->location->radius_meter ?? 0));
                                            $empNameSafe = addslashes(Auth::user()->name);
                                        @endphp
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $date }}</td>

                                            {{-- Detail Masuk --}}
                                            <td>
                                                <div class="presence-item">
                                                    <div class="presence-photo">
                                                        @if ($clockIn && $clockIn->clock_in_photo_url)
                                                            <img src="{{ $clockIn->clock_in_photo_path }}" alt="Foto Masuk">
                                                        @else
                                                            <div class="presence-avatar">IN</div>
                                                        @endif
                                                    </div>
                                                    <div class="presence-meta">
                                                        <div class="presence-line">
                                                            <span class="time">{{ $inTime }}</span>
                                                            @if (!is_null($inLat) && !is_null($inLng))
                                                                <span class="dot">•</span>
                                                                <button type="button" class="link-map"
                                                                    onclick="showMapWithRadius({{ $inLat }}, {{ $inLng }}, 'Lokasi Masuk - {{ $empNameSafe }}', {{ $radiusMeter }})">
                                                                    Lihat Peta
                                                                </button>
                                                            @endif
                                                        </div>
                                                        @if (!is_null($inLat) && !is_null($inLng))
                                                            <div class="coords">({{ $inLat }}, {{ $inLng }})
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>

                                            {{-- Detail Keluar --}}
                                            <td>
                                                <div class="presence-item">
                                                    <div class="presence-photo">
                                                        @if ($clockOut && $clockOut->clock_out_photo_url)
                                                            <img src="{{ $clockOut->clock_out_photo_path }}"
                                                                alt="Foto Keluar">
                                                        @else
                                                            <div class="presence-avatar out">OUT</div>
                                                        @endif
                                                    </div>
                                                    <div class="presence-meta">
                                                        <div class="presence-line">
                                                            <span class="time">{{ $outTime }}</span>
                                                            @if (!is_null($outLat) && !is_null($outLng))
                                                                <span class="dot">•</span>
                                                                <button type="button" class="link-map"
                                                                    onclick="showMapWithRadius({{ $outLat }}, {{ $outLng }}, 'Lokasi Keluar - {{ $empNameSafe }}', {{ $radiusMeter }})">
                                                                    Lihat Peta
                                                                </button>
                                                            @endif
                                                        </div>
                                                        @if (!is_null($outLat) && !is_null($outLng))
                                                            <div class="coords">({{ $outLat }}, {{ $outLng }})
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Peta OSM --}}
        <div class="modal fade" id="mapModal" tabindex="-1" aria-labelledby="mapModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title m-0" id="mapModalLabel">Lokasi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body p-0">
                        <div id="leafletMap" class="leaflet-map"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- ==== CSS ==== --}}
@section('css')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <style>
        .leaflet-map {
            height: 420px;
            width: 100%
        }

        .presence-item {
            display: flex;
            gap: .75rem;
            align-items: center
        }

        .presence-photo {
            width: 56px;
            height: 56px;
            border-radius: .75rem;
            overflow: hidden;
            background: #f1f3f5;
            border: 1px solid #e9ecef;
            flex: 0 0 auto
        }

        .presence-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block
        }

        .presence-avatar {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: .75rem;
            letter-spacing: .5px;
            color: #5f6b7a;
            background: #e9f5ff
        }

        .presence-avatar.out {
            background: #fff4e6
        }

        .presence-meta {
            min-width: 150px
        }

        .presence-line {
            display: flex;
            align-items: center;
            gap: .5rem;
            line-height: 1
        }

        .presence-line .time {
            font-weight: 600
        }

        .presence-line .dot {
            color: #c0c6cf;
            font-size: 1rem;
            line-height: 1
        }

        .link-map {
            border: 0;
            background: none;
            padding: 0;
            margin: 0;
            color: #0d6efd;
            text-decoration: none;
            cursor: pointer;
            font-weight: 600
        }

        .link-map:hover {
            text-decoration: underline
        }

        .coords {
            font-size: .8rem;
            color: #6c757d;
            margin-top: .25rem
        }

        td {
            vertical-align: middle
        }
    </style>
@endsection

{{-- ==== SCRIPTS ==== --}}
@section('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        function showMapWithRadius(lat, lng, title, radiusMeter) {
            const modalEl = document.getElementById('mapModal');
            const mapEl = document.getElementById('leafletMap');
            document.getElementById('mapModalLabel').textContent = title || 'Lokasi';
            const bsModal = new bootstrap.Modal(modalEl);
            const onShown = () => {
                let map = mapEl._leaflet_instance || null;
                if (!map) {
                    map = L.map(mapEl).setView([lat, lng], 17);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; OpenStreetMap contributors'
                    }).addTo(map);
                    map._marker = L.marker([lat, lng]).addTo(map);
                    mapEl._leaflet_instance = map;
                } else {
                    map.setView([lat, lng], 17);
                    if (map._marker) {
                        map._marker.setLatLng([lat, lng]);
                    } else {
                        map._marker = L.marker([lat, lng]).addTo(map);
                    }
                }
                if (map._circle) {
                    map.removeLayer(map._circle);
                    map._circle = null;
                }
                if (radiusMeter && radiusMeter > 0) {
                    map._circle = L.circle([lat, lng], {
                        radius: radiusMeter
                    }).addTo(map);
                }
                setTimeout(() => map.invalidateSize(), 250);
                modalEl.removeEventListener('shown.bs.modal', onShown);
            };
            modalEl.addEventListener('shown.bs.modal', onShown);
            bsModal.show();
        }
    </script>
@endsection
