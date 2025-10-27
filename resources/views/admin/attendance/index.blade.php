@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        .presence-item {
            display: flex;
            gap: .75rem;
            align-items: center;
        }

        .presence-photo {
            width: 56px;
            height: 56px;
            border-radius: .75rem;
            overflow: hidden;
            border: 1px solid #e9ecef;
            background: #f8f9fa;
        }

        .presence-avatar {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: .75rem;
        }

        .presence-avatar.out {
            background: #fff4e6;
        }

        .link-map {
            border: 0;
            background: none;
            color: #0d6efd;
            cursor: pointer;
            font-weight: 600;
        }

        .coords {
            font-size: .8rem;
            color: #6c757d;
            margin-top: .25rem;
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4">Riwayat Presensi Pegawai</h4>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.attendance.handlePeriode') }}" method="POST" class="my-3" id="periodeForm">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="row">
                                <label for="start_date" class="col-sm-3 col-form-label col-form-label-sm">Mulai</label>
                                <div class="col-sm-9">
                                    <input type="date" name="start_date" id="start_date"
                                        class="form-control form-control-sm @error('start_date') is-invalid @enderror"
                                        value="{{ old('start_date', '') }}">
                                    @error('start_date')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="row">
                                <label for="end_date" class="col-sm-3 col-form-label col-form-label-sm">Selesai</label>
                                <div class="col-sm-9">
                                    <input type="date" name="end_date" id="end_date"
                                        class="form-control form-control-sm @error('end_date') is-invalid @enderror"
                                        value="{{ old('end_date', '') }}">
                                    @error('end_date')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 d-flex gap-2 align-items-start">
                            <button type="submit" name="action" value="export" class="btn btn-info btn-sm">
                                <i class="bx bx-download"></i> Export
                            </button>
                            <button type="button" class="btn btn-danger btn-sm swalDeleteData">
                                <i class="tf-icons bx bx-trash text-white"></i> Hapus
                            </button>
                        </div>
                    </div>
                </form>

                <table id="attendanceTable" class="table table-bordered table-hover align-middle table-sm"
                    style="font-size:80%">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Lokasi</th>
                            <th>Tanggal</th>
                            <th>Detail Masuk</th>
                            <th>Detail Keluar</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

        {{-- Modal Foto --}}
        <div class="modal fade" id="photoModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content bg-transparent border-0 shadow-none">
                    <button type="button" class="btn-close ms-auto me-2 mt-2" data-bs-dismiss="modal"></button>
                    <div class="modal-body text-center p-0">
                        <img id="photoModalImg" src="" class="img-fluid rounded shadow">
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Peta --}}
        <div class="modal fade" id="mapModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="mapModalLabel">Lokasi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-0">
                        <div id="leafletMap" style="height:420px"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        $(function() {
            const table = $('#attendanceTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.attendance.data') }}",
                    data: function(d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nama',
                        name: 'nama'
                    },
                    {
                        data: 'lokasi',
                        name: 'lokasi'
                    },
                    {
                        data: 'tanggal',
                        name: 'tanggal'
                    },
                    {
                        data: 'detail_masuk',
                        name: 'detail_masuk',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'detail_keluar',
                        name: 'detail_keluar',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            $('#start_date, #end_date').change(function() {
                table.ajax.reload();
            });
        });

        // Modal foto
        function showPhotoModal(url) {
            $('#photoModalImg').attr('src', url);
            new bootstrap.Modal('#photoModal').show();
        }

        // Modal peta
        function showMapWithRadius(lat, lng, title, radiusMeter) {
            const modalEl = document.getElementById('mapModal');
            const mapEl = document.getElementById('leafletMap');
            document.getElementById('mapModalLabel').textContent = title || 'Lokasi';
            const bsModal = new bootstrap.Modal(modalEl);

            modalEl.addEventListener('shown.bs.modal', function onShown() {
                let map = mapEl._leaflet_instance || null;
                if (!map) {
                    map = L.map(mapEl).setView([lat, lng], 17);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
                    map._marker = L.marker([lat, lng]).addTo(map);
                    mapEl._leaflet_instance = map;
                } else {
                    map.setView([lat, lng], 17);
                    if (map._marker) map._marker.setLatLng([lat, lng]);
                }
                if (map._circle) map.removeLayer(map._circle);
                if (radiusMeter > 0) {
                    map._circle = L.circle([lat, lng], {
                        radius: radiusMeter
                    }).addTo(map);
                }
                setTimeout(() => map.invalidateSize(), 250);
                modalEl.removeEventListener('shown.bs.modal', onShown);
            });
            bsModal.show();
        }
    </script>
@endsection
