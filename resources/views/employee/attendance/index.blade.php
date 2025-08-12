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
                                        @endphp
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $clockIn?->user->name ?? ($clockOut?->user->name ?? '-') }}</td>
                                            <td>{{ $date }}</td>
                                            <td>{{ $clockIn && $clockIn->clock_in_time ? \Carbon\Carbon::parse($clockIn->clock_in_time)->format('H:i') : '-' }}
                                            </td>
                                            <td>
                                                @if ($clockIn && $clockIn->clock_in_photo_url)
                                                    <img src="{{ $clockIn->clock_in_photo_path }}" alt="Foto Masuk"
                                                        width="60">
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{ $clockIn->clock_in_latitude ?? '-' }},
                                                {{ $clockIn->clock_in_longitude ?? '-' }}</td>
                                            <td>{{ $clockOut && $clockOut->clock_out_time ? \Carbon\Carbon::parse($clockOut->clock_out_time)->format('H:i') : '-' }}
                                            </td>
                                            <td>
                                                @if ($clockOut && $clockOut->clock_out_photo_url)
                                                    <img src="{{ $clockIn->clock_out_photo_path }}" alt="Foto Keluar"
                                                        width="60">
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{ $clockOut->clock_out_latitude ?? '-' }},
                                                {{ $clockOut->clock_out_longitude ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- <div class="my-4 px-3">
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
        </div> --}}
    </div>
@endsection
