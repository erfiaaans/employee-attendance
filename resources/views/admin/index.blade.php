@extends('layouts.app')
@push('styles')
    {{-- <link rel="stylesheet" href="{{ asset('css/dashboardStyle.css') }}"> --}}
@endpush
@php
    $stats = $stats ?? [
        'totalEmployees' => 0,
        'totalLocations' => 0,
        'totalAttendance' => 0,
        'todayAttendance' => 0,
    ];
@endphp
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4">
            <a href="#"><span class="text-muted fw-light">{{ __('Dashboards') }} /</span>
            </a>
            <a href="#" class="text-secondary">
                {{ __('Master User') }} /</a> {{ __('Detail') }}
        </h4>
        <div class="row gy-4">
            <div class="col-12">
                <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-4">
                    <div class="col">
                        <a href="{{ route('admin.profile.index') }}" class="text-decoration-none text-dark">
                            <div class="card square-card h-100">
                                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                                    <img src="{{ asset('img/icons/user.png') }}" alt="Profil" class="icon-img mb-2" />
                                    <p class="mb-0 fw-bold">Profil</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col">
                        <a href="{{ route('admin.location') }}" class="text-decoration-none text-dark">
                            <div class="card square-card h-100">
                                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                                    <img src="{{ asset('img/icons/office.png') }}" alt="Office" class="icon-img mb-3" />
                                    <p class="mb-0 fw-bold">Office</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col">
                        <a href="{{ route('admin.location') }}" class="text-decoration-none text-dark">
                            <div class="card square-card h-100">
                                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                                    <img src="{{ asset('img/icons/lokasi_user.png') }}" alt="Office"
                                        class="icon-img mb-3" />
                                    <p class="mb-0 fw-bold">Lokasi Pegawai</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col">
                        <a href="{{ route('admin.user') }}" class="text-decoration-none text-dark">
                            <div class="card square-card h-100">
                                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                                    <img src="{{ asset('img/icons/daftar-pegawai.png') }}" alt="Daftar Pegawai"
                                        class="icon-img mb-3" />
                                    <p class="mb-0 fw-bold">Daftar Pegawai</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col">
                        <a href="{{ route('admin.attendance') }}" class="text-decoration-none text-dark">
                            <div class="card square-card h-100">
                                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                                    <img src="{{ asset('img/icons/absensi.png') }}" alt="Riwayat Absensi"
                                        class="icon-img mb-3" />
                                    <p class="mb-0 fw-bold">Riwayat Absensi</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-12 mt-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between">
                                <div class="card-title mb-0">
                                    <h5 class="mb-1 me-2">Statistik Kehadiran</h5>
                                </div>
                                <div class="dropdown">
                                    <button class="btn text-body-secondary p-0" type="button" id="orederStatistics"
                                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="icon-base bx bx-dots-vertical-rounded icon-lg"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="orederStatistics">
                                        <a class="dropdown-item" href="javascript:void(0);"
                                            id="btn-refresh-stats">Refresh</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <ul class="p-0 m-0">
                                    <li class="d-flex align-items-center mb-5">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <span class="avatar-initial rounded bg-label-primary">
                                                <i class="icon-base bx bx-mobile-alt"></i>
                                            </span>
                                        </div>
                                        <div
                                            class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                            <div class="me-2">
                                                <h6 class="mb-0">Total Keseluruhan Karyawan</h6>
                                            </div>
                                            <div class="user-progress">
                                                <h6 id="stat-total-employees" class="mb-0">
                                                    {{ number_format($stats['totalEmployees']) }}</h6>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="d-flex align-items-center mb-5">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <span class="avatar-initial rounded bg-label-success">
                                                <i class="icon-base bx bx-closet"></i>
                                            </span>
                                        </div>
                                        <div
                                            class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                            <div class="me-2">
                                                <h6 class="mb-0">Total Lokasi Kantor Onmeso</h6>
                                            </div>
                                            <div class="user-progress">
                                                <h6 id="stat-total-locations" class="mb-0">
                                                    {{ number_format($stats['totalLocations']) }}</h6>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="d-flex align-items-center mb-5">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <span class="avatar-initial rounded bg-label-info">
                                                <i class="icon-base bx bx-home-alt"></i>
                                            </span>
                                        </div>
                                        <div
                                            class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                            <div class="me-2">
                                                <h6 class="mb-0">Total Presensi</h6>
                                            </div>
                                            <div class="user-progress">
                                                <h6 id="stat-total-attendance" class="mb-0">
                                                    {{ number_format($stats['totalAttendance']) }}</h6>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="d-flex align-items-center">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <span class="avatar-initial rounded bg-label-secondary">
                                                <i class="icon-base bx bx-football"></i>
                                            </span>
                                        </div>
                                        <div
                                            class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                            <div class="me-2">
                                                <h6 class="mb-0">Total Presensi Hari Ini</h6>
                                            </div>
                                            <div class="user-progress">
                                                <h6 id="stat-today-attendance" class="mb-0">
                                                    {{ number_format($stats['todayAttendance']) }}</h6>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between">
                                <div class="card-title mb-0">
                                    <h5 class="mb-1 me-2">Presensi Hari Ini</h5>
                                </div>
                                <div class="dropdown">
                                    <button class="btn text-body-secondary p-0" type="button" id="orederStatistics"
                                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="icon-base bx bx-dots-vertical-rounded icon-lg"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="orederStatistics">
                                        <a class="dropdown-item" href="javascript:void(0);"
                                            id="btn-refresh-stats">Refresh</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <ul class="p-0 m-0">
                                    <li class="d-flex align-items-center mb-5">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <span class="avatar-initial rounded bg-label-primary">
                                                <i class="icon-base bx bx-mobile-alt"></i>
                                            </span>
                                        </div>
                                        <div
                                            class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                            <div class="me-2">
                                                <h6 class="mb-0">Total Karyawan Yang Sudah Melakukan Clock In</h6>
                                            </div>
                                            <div class="user-progress">
                                                <h6 id="stat-total-employees" class="mb-0">
                                                    {{ number_format($stats['totalEmployees']) }}</h6>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="d-flex align-items-center mb-5">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <span class="avatar-initial rounded bg-label-success">
                                                <i class="icon-base bx bx-closet"></i>
                                            </span>
                                        </div>
                                        <div
                                            class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                            <div class="me-2">
                                                <h6 class="mb-0">Total Karyawan Yang Sudah Melakukan Clock Out</h6>
                                            </div>
                                            <div class="user-progress">
                                                <h6 id="stat-total-locations" class="mb-0">
                                                    {{ number_format($stats['totalLocations']) }}</h6>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="d-flex align-items-center mb-5">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <span class="avatar-initial rounded bg-label-info">
                                                <i class="icon-base bx bx-home-alt"></i>
                                            </span>
                                        </div>
                                        <div
                                            class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                            <div class="me-2">
                                                <h6 class="mb-0">Total Karyawan Yang Masuk Hari Ini</h6>
                                            </div>
                                            <div class="user-progress">
                                                <h6 id="stat-total-attendance" class="mb-0">
                                                    {{ number_format($stats['totalAttendance']) }}</h6>
                                            </div>
                                        </div>
                                    </li>
                                    {{-- <li class="d-flex align-items-center">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <span class="avatar-initial rounded bg-label-secondary">
                                                <i class="icon-base bx bx-football"></i>
                                            </span>
                                        </div>
                                        <div
                                            class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                            <div class="me-2">
                                                <h6 class="mb-0">Total Presensi Hari Ini</h6>
                                            </div>
                                            <div class="user-progress">
                                                <h6 id="stat-today-attendance" class="mb-0">
                                                    {{ number_format($stats['todayAttendance']) }}</h6>
                                            </div>
                                        </div>
                                    </li> --}}
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        const fmt = (n) => new Intl.NumberFormat().format(n);
        document.getElementById('btn-refresh-stats')?.addEventListener('click', async () => {
            try {
                const res = await fetch('{{ route('admin.dashboard.stats') }}', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                if (!res.ok) throw new Error('Gagal mengambil data');
                const d = await res.json();
                const el = (id) => document.getElementById(id);
                el('stat-total-employees').textContent = fmt(d.totalEmployees ?? 0);
                el('stat-total-locations').textContent = fmt(d.totalLocations ?? 0);
                el('stat-total-attendance').textContent = fmt(d.totalAttendance ?? 0);
                el('stat-today-attendance').textContent = fmt(d.todayAttendance ?? 0);
            } catch (e) {
                alert(e.message);
            }
        });
    </script>
@endpush
