@extends('layouts.app')
@section('css')
    {{-- Leaflet CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">

    <style>
        .leaflet-map {
            height: 420px;
            width: 100%
        }

        /* Kartu kecil: foto kiri, info kanan */
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
            min-width: 180px
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

        /* Sedikit rapi untuk sel */
        td {
            vertical-align: middle
        }
    </style>
@endsection

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
                        <h5 class="mb-0">{{ __('Riwayat Absensi Pegawai') }}</h5>
                    </div>

                    <div class="card-body">
                        {{-- Form Pencarian --}}
                        <form method="GET" action="{{ route('admin.attendance') }}">
                            <div class="row mb-2">
                                <div class="col-md-12">
                                    <div class="d-flex align-items-center flex-wrap gap-2">
                                        <input type="text" name="search" class="form-control me-2"
                                            style="max-width: 250px;" placeholder="Cari nama, jabatan, kantor, tanggal..."
                                            value="{{ request('search') }}">
                                        @if (request('filter'))
                                            <input type="hidden" name="filter" value="{{ request('filter') }}">
                                        @endif
                                        <button type="submit" class="btn btn-light">Cari</button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        {{-- Export Periode --}}
                        <form action="{{ route('admin.attendance.exportByPeriode') }}" method="GET" class="my-3">
                            <div class="row g-2 align-items-end">
                                <div class="col-md-3">
                                    <label for="start_date_export" class="form-label">Tanggal Mulai</label>
                                    <input type="date" name="start_date" id="start_date_export" class="form-control"
                                        value="{{ request('start_date') }}" required>
                                </div>
                                <div class="col-md-3">
                                    <label for="end_date_export" class="form-label">Tanggal Selesai</label>
                                    <input type="date" name="end_date" id="end_date_export" class="form-control"
                                        value="{{ request('end_date') }}" required>
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-success">
                                        <i class="bx bx-download"></i> Export Periode
                                    </button>
                                </div>
                            </div>
                        </form>

                        {{-- Hapus Periode --}}
                        <form action="{{ route('admin.attendance.destroyByPeriode') }}" method="POST" class="my-3">
                            @csrf
                            @method('DELETE')
                            <div class="row g-2 align-items-end">
                                <div class="col-md-3">
                                    <label for="start_date" class="form-label">Tanggal Mulai</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control" required>
                                </div>
                                <div class="col-md-3">
                                    <label for="end_date" class="form-label">Tanggal Selesai</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control" required>
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-icn btn-danger swalDeleteData">
                                        <i class="tf-icons bx bx-trash text-white"></i>Hapus Berdasarkan Periode
                                    </button>
                                </div>
                            </div>
                        </form>

                        {{-- Tabel Riwayat --}}
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Pegawai</th>
                                        <th>Lokasi Absensi</th>
                                        <th>Tanggal</th>
                                        <th>Detail Masuk</th>
                                        <th>Detail Keluar</th>
                                        <th style="width:110px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($attendances as $index => $attendance)
                                        @php
                                            $officeName = $attendance->location->office_name ?? '-';
                                            $radiusMeter = (float) ($attendance->location->radius ?? 0);
                                            $empNameSafe = addslashes($attendance->user->name ?? 'Pegawai');

                                            $inTimeObj = $attendance->clock_in_time
                                                ? \Carbon\Carbon::parse($attendance->clock_in_time)
                                                : null;
                                            $outTimeObj = $attendance->clock_out_time
                                                ? \Carbon\Carbon::parse($attendance->clock_out_time)
                                                : null;
                                            $inTime = $inTimeObj ? $inTimeObj->format('H:i') : '-';
                                            $outTime = $outTimeObj ? $outTimeObj->format('H:i') : '-';
                                            $date = $inTimeObj
                                                ? $inTimeObj->format('Y-m-d')
                                                : ($outTimeObj
                                                    ? $outTimeObj->format('Y-m-d')
                                                    : '-');

                                            $inLat = $attendance->clock_in_latitude;
                                            $inLng = $attendance->clock_in_longitude;
                                            $outLat = $attendance->clock_out_latitude;
                                            $outLng = $attendance->clock_out_longitude;
                                        @endphp

                                        <tr>
                                            <td>{{ $index + $attendances->firstItem() }}</td>
                                            <td>{{ $attendance->user->name ?? '-' }}</td>
                                            <td>{{ $officeName }}</td>
                                            <td>{{ $date }}</td>

                                            {{-- Detail Masuk --}}
                                            <td>
                                                <div class="presence-item">
                                                    <div class="presence-photo">
                                                        @if ($attendance->clock_in_photo_url)
                                                            <img src="{{ $attendance->clock_in_photo_path }}"
                                                                alt="Clock In">
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
                                                                    title="Lihat peta lokasi masuk"
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
                                                        @if ($attendance->clock_out_photo_url)
                                                            <img src="{{ $attendance->clock_out_photo_path }}"
                                                                alt="Clock Out">
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
                                                                    title="Lihat peta lokasi keluar"
                                                                    onclick="showMapWithRadius({{ $outLat }}, {{ $outLng }}, 'Lokasi Keluar - {{ $empNameSafe }}', {{ $radiusMeter }})">
                                                                    Lihat Peta
                                                                </button>
                                                            @endif
                                                        </div>
                                                        @if (!is_null($outLat) && !is_null($outLng))
                                                            <div class="coords">({{ $outLat }},
                                                                {{ $outLng }})</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>

                                            {{-- Aksi --}}
                                            <td class="text-center">
                                                <div class="btn-group">
                                                    <form
                                                        action="{{ route('admin.attendance.destroy', $attendance->attendance_id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="btn btn-icon btn-danger btn-sm swalDeleteData">
                                                            <i class="tf-icons bx bx-trash text-white"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">Data absensi tidak ditemukan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="my-4 px-3">
            <nav aria-label="...">
                <ul class="pagination">
                    <li class="page-item {{ $attendances->onFirstPage() ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ $attendances->previousPageUrl() ?? '#' }}">Previous</a>
                    </li>
                    @for ($i = 1; $i <= $attendances->lastPage(); $i++)
                        <li class="page-item {{ $i == $attendances->currentPage() ? 'active' : '' }}">
                            <a class="page-link" href="{{ $attendances->url($i) }}">{{ $i }}</a>
                        </li>
                    @endfor
                    <li class="page-item {{ $attendances->hasMorePages() ? '' : 'disabled' }}">
                        <a class="page-link" href="{{ $attendances->nextPageUrl() ?? '#' }}">Next</a>
                    </li>
                </ul>
            </nav>
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

@section('scripts')
    {{-- Leaflet JS --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
        // Tampilkan peta pada modal, reuse instance untuk performa
        function showMapWithRadius(lat, lng, title, radiusMeter) {
            const modalEl = document.getElementById('mapModal');
            const mapEl = document.getElementById('leafletMap');
            const titleEl = document.getElementById('mapModalLabel');
            titleEl.textContent = title || 'Lokasi';

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

                // radius geofence (opsional)
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
