@extends('layouts.app')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4">
            <a href="#"><span class="text-muted fw-light">{{ __('Dashboards') }} /</span></a>
            <a href="#" class="text-secondary">{{ __('Riwayat Absensi') }}</a>
        </h4>
        <form method="GET" action="{{ route('admin.attendance') }}" class="mb-4">
            <div class="row">
                <div class="card md-12">
                    <div class="col-mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">{{ __('Riwayat Absensi Pegawai') }}</h5>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="{{ route('admin.user') }}">
                                <div class="row mb-2">
                                    <div class="col-md-12">
                                        <div class="d-flex align-items-center flex-wrap gap-2">
                                            <input type="text" name="search" class="form-control me-2"
                                                style="max-width: 250px;"
                                                placeholder="Cari nama pegawai, jabatan, kantor..."
                                                value="{{ request('search') }}">
                                            <button type="submit" class="btn btn-light btn-sm">Cari</button>
                                            <select name="filter" class="form-select form-select-sm"
                                                style="max-width: 150px;" onchange="this.form.submit()">
                                                <option value="">Tampilkan Semua</option>
                                                <option value="clock_in"
                                                    {{ request('filter') == 'clock_in' ? 'selected' : '' }}>
                                                    Clock In
                                                </option>
                                                <option value="clock_out"
                                                    {{ request('filter') == 'clock_out' ? 'selected' : '' }}>
                                                    Clock Out
                                                </option>
                                            </select>
                                            <div class="dropdown">
                                                <button class="btn btn-secondary btn-sm dropdown-toggle" type="button"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    Pilih Pegawai
                                                </button>
                                                <ul class="dropdown-menu">
                                                    @foreach ($allUsers as $user)
                                                        <li>
                                                            <a class="dropdown-item"
                                                                href="{{ route('admin.user', ['search' => $user->name]) }}">{{ $user->name }}</a>
                                                        </li>
                                                    @endforeach
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
                                            <th>Jabatan</th>
                                            <th>Kantor</th>
                                            <th>Waktu</th>
                                            <th>Foto</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($attendances as $index => $attendance)
                                            <tr>
                                                <td>{{ $index + $attendances->firstItem() }}</td>
                                                <td>{{ $attendance->user->name ?? '-' }}</td>
                                                <td>{{ $attendance->user->position ?? '-' }}</td>
                                                <td>{{ $attendance->location->office_name ?? '-' }}</td>
                                                <td>
                                                    @if ($filter == 'clock_out')
                                                        {{ $attendance->clock_out_time ?? '-' }}
                                                    @else
                                                        {{ $attendance->clock_in_time ?? '-' }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($filter == 'clock_out')
                                                        @if ($attendance->clock_out_photo_url)
                                                            <img src="{{ $attendance->clock_out_photo_url }}"
                                                                alt="Foto Clock Out" width="60">
                                                        @else
                                                            -
                                                        @endif
                                                    @else
                                                        @if ($attendance->clock_in_photo_url)
                                                            <img src="{{ $attendance->clock_in_photo_url }}"
                                                                alt="Foto Clock In" width="60">
                                                        @else
                                                            -
                                                        @endif
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">Data absensi tidak ditemukan.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <div class="d-flex justify-content-end">
            {{ $attendances->withQueryString()->links() }}
        </div>
    </div>
@endsection
