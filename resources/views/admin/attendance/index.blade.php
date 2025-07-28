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
                        <h5 class="mb-0">{{ __('Riwayat Absensi Pegawai') }}</h5>
                    </div>
                    <div class="card-body">
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

                                        <button type="submit" class="btn btn-light btn-sm">Cari</button>

                                        <div class="dropdown">
                                            <button class="btn btn-secondary btn-sm dropdown-toggle" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                Pilih Pegawai
                                            </button>
                                            <ul class="dropdown-menu">
                                                @foreach ($allUsers as $user)
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('admin.attendance', ['filter' => $user->name, 'search' => request('search')]) }}">
                                                            {{ $user->name }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                                @if (request('filter'))
                                                    <li>
                                                        <hr class="dropdown-divider">
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item text-danger"
                                                            href="{{ route('admin.attendance') }}">
                                                            Hapus Filter
                                                        </a>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Pegawai</th>
                                        <th>Tanggal</th>
                                        <th>Jam Masuk</th>
                                        <th>Foto Masuk</th>
                                        <th>Lokasi Masuk</th>
                                        <th>Jam Keluar</th>
                                        <th>Foto Keluar</th>
                                        <th>Lokasi Keluar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($attendances as $index => $attendance)
                                        <tr>
                                            <td>{{ $index + $attendances->firstItem() }}</td>
                                            <td>{{ $attendance->user->name ?? '-' }}</td>
                                            <td>{{ \Carbon\Carbon::parse($attendance->clock_in_time)->format('Y-m-d') }}
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($attendance->clock_in_time)->format('H:i') }}</td>
                                            <td>
                                                @if ($attendance->clock_in_photo_url)
                                                    <img src="{{ $attendance->clock_in_photo_url }}" alt="Clock In"
                                                        width="60">
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                {{ $attendance->clock_in_latitude ?? '-' }},
                                                {{ $attendance->clock_in_longitude ?? '-' }}
                                            </td>
                                            <td>
                                                {{ $attendance->clock_out_time ? \Carbon\Carbon::parse($attendance->clock_out_time)->format('H:i') : '-' }}
                                            </td>
                                            <td>
                                                @if ($attendance->clock_out_photo_url)
                                                    <img src="{{ $attendance->clock_out_photo_url }}" alt="Clock Out"
                                                        width="60">
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                {{ $attendance->clock_out_latitude ?? '-' }},
                                                {{ $attendance->clock_out_longitude ?? '-' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center">Data absensi tidak ditemukan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
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
    </div>
@endsection
