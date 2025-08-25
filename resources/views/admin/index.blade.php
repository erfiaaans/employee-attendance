@extends('layouts.app')

@section('css')
    <style>
        .square-card {
            min-height: 120px;
        }

        .icon-img {
            width: 40px;
            height: 40px;
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row gy-4">
            <div class="col-12">
                <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-4">
                    <div class="col">
                        <a href="{{ route('admin.profile.index') }}" class="text-decoration-none text-dark">
                            <div class="card square-card h-100 bg-label-primary">
                                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                                    <img src="{{ asset('img/icons/user.png') }}" alt="Profil" class="icon-img mb-2" />
                                    <p class="mb-0 fw-bold">Profil</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col">
                        <a href="{{ route('admin.location') }}" class="text-decoration-none text-dark">
                            <div class="card square-card h-100 bg-label-danger">
                                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                                    <img src="{{ asset('img/icons/office.png') }}" alt="Office" class="icon-img mb-3" />
                                    <p class="mb-0 fw-bold">Office</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col">
                        <a href="{{ route('admin.location') }}" class="text-decoration-none text-dark">
                            <div class="card square-card h-100 bg-label-success">
                                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                                    <img src="{{ asset('img/icons/lokasi_user.png') }}" alt="Lokasi Pegawai"
                                        class="icon-img mb-3" />
                                    <p class="mb-0 fw-bold">Lokasi Pegawai</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col">
                        <a href="{{ route('admin.user') }}" class="text-decoration-none text-dark">
                            <div class="card square-card h-100 bg-label-warning">
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
                            <div class="card square-card h-100 bg-label-info">
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

            <!-- Filter tanggal & Refresh -->
            <div class="col-12">
                <div class="card mt-2">
                    <div class="card-body">
                        <form class="row g-3 align-items-end">
                            <div class="col-auto">
                                <label class="form-label mb-0">Dari</label>
                                <input type="date" class="form-control" id="from">
                            </div>
                            <div class="col-auto">
                                <label class="form-label mb-0">Sampai</label>
                                <input type="date" class="form-control" id="to">
                            </div>
                            <div class="col-auto">
                                <button type="button" class="btn btn-primary" id="apply-filter">Terapkan</button>
                            </div>
                            <div class="col-auto ms-auto">
                                <button type="button" class="btn btn-outline-secondary"
                                    id="btn-refresh-stats">Refresh</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- KPI Cards -->
            <div class="col-12 mt-2">
                <div class="row g-4">
                    <!-- Statistik Kehadiran -->
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between">
                                <div class="card-title mb-0">
                                    <h5 class="mb-1 me-2">Statistik Kehadiran</h5>
                                </div>
                            </div>
                            <div class="card-body">
                                <ul class="p-0 m-0">
                                    <li class="d-flex align-items-center mb-5">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <span class="avatar-initial rounded bg-label-primary">
                                                <i class="icon-base bx bx-user"></i>
                                            </span>
                                        </div>
                                        <div
                                            class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                            <div class="me-2">
                                                <h6 class="mb-0">Total Keseluruhan Karyawan</h6>
                                            </div>
                                            <div class="user-progress">
                                                <h6 id="stat-total-employees" class="mb-0">
                                                    {{ number_format($stats->totalEmployees) }}</h6>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="d-flex align-items-center mb-5">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <span class="avatar-initial rounded bg-label-success">
                                                <i class="icon-base bx bx-building-house"></i>
                                            </span>
                                        </div>
                                        <div
                                            class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                            <div class="me-2">
                                                <h6 class="mb-0">Total Lokasi Kantor</h6>
                                            </div>
                                            <div class="user-progress">
                                                <h6 id="stat-total-locations" class="mb-0">
                                                    {{ number_format($stats->totalLocations) }}</h6>
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
                                                <h6 id="stat-total-attendances" class="mb-0">
                                                    {{ number_format($stats->totalAttendances) }}</h6>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="d-flex align-items-center">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <span class="avatar-initial rounded bg-label-secondary">
                                                <i class="icon-base bx bx-time"></i>
                                            </span>
                                        </div>
                                        <div
                                            class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                            <div class="me-2">
                                                <h6 class="mb-0">Total Presensi Hari Ini (In & Out)</h6>
                                            </div>
                                            <div class="user-progress">
                                                <h6 id="stat-today-both" class="mb-0">
                                                    {{ number_format($stats->todayAttendanceBoth) }}</h6>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Presensi Hari Ini -->
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between">
                                <div class="card-title mb-0">
                                    <h5 class="mb-1 me-2">Presensi Hari Ini</h5>
                                </div>
                            </div>
                            <div class="card-body">
                                <ul class="p-0 m-0">
                                    <li class="d-flex align-items-center mb-5">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <span class="avatar-initial rounded bg-label-primary">
                                                <i class="icon-base bx bx-log-in-circle"></i>
                                            </span>
                                        </div>
                                        <div
                                            class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                            <div class="me-2">
                                                <h6 class="mb-0">Karyawan Clock In</h6>
                                            </div>
                                            <div class="user-progress">
                                                <h6 id="stat-today-clockin" class="mb-0">
                                                    {{ number_format($stats->todayClockInOnly) }}</h6>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="d-flex align-items-center mb-5">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <span class="avatar-initial rounded bg-label-success">
                                                <i class="icon-base bx bx-log-out-circle"></i>
                                            </span>
                                        </div>
                                        <div
                                            class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                            <div class="me-2">
                                                <h6 class="mb-0">Karyawan Clock Out</h6>
                                            </div>
                                            <div class="user-progress">
                                                <h6 id="stat-today-clockout" class="mb-0">
                                                    {{ number_format($stats->todayClockOutOnly) }}</h6>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                                <div class="small text-muted">Clock-in hari ini (distinct user): <strong
                                        id="txt-today-in">{{ number_format($stats->todayClockInOnly) }}</strong>,
                                    Clock-out: <strong
                                        id="txt-today-out">{{ number_format($stats->todayClockOutOnly) }}</strong>,
                                    In & Out: <strong
                                        id="txt-today-both">{{ number_format($stats->todayAttendanceBoth) }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row Grafik -->
            <div class="col-12 mt-2">
                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="mb-0">Tren Kehadiran (14 Hari / Range)</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="chart-trend" height="120"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="mb-0">Komposisi Hari Ini</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="chart-today" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="mb-0">Kehadiran per Lokasi (Bulan Ini)</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="chart-location" height="140"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="mb-0">Ketepatan Waktu</h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled mb-0">
                                    <li class="d-flex justify-content-between mb-2">
                                        <span>Rata-rata Clock In (range)</span><strong id="stat-avg-clockin">–</strong>
                                    </li>
                                    <li class="d-flex justify-content-between">
                                        <span>Jumlah Terlambat (range)</span><strong id="stat-late-count">0</strong>
                                    </li>
                                </ul>
                                <small class="text-muted d-block mt-2">Patokan terlambat: 09:00</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div> <!-- /row -->
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const fmt = (n) => new Intl.NumberFormat().format(n);
        const el = (id) => document.getElementById(id);

        let chartTrend, chartToday, chartLocation;

        async function loadStats(params = {}) {
            const qs = new URLSearchParams(params).toString();
            const url = `{{ route('admin.dashboard.stats') }}${qs ? ('?' + qs) : ''}`;
            const res = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            if (!res.ok) throw new Error('Gagal mengambil data statistik');
            const d = await res.json();

            // KPI dasar
            el('stat-total-employees').textContent = fmt(d.totalEmployees ?? 0);
            el('stat-total-locations').textContent = fmt(d.totalLocations ?? 0);
            el('stat-total-attendances').textContent = fmt(d.totalAttendances ?? 0);

            // Hari ini
            el('stat-today-both').textContent = fmt(d.todayAttendanceBoth ?? 0);
            el('stat-today-clockin').textContent = fmt(d.todayClockInOnly ?? 0);
            el('stat-today-clockout').textContent = fmt(d.todayClockOutOnly ?? 0);

            el('txt-today-in').textContent = fmt(d.todayClockInOnly ?? 0);
            el('txt-today-out').textContent = fmt(d.todayClockOutOnly ?? 0);
            el('txt-today-both').textContent = fmt(d.todayAttendanceBoth ?? 0);

            // Ketepatan waktu
            el('stat-avg-clockin').textContent = d.avgClockIn ?? '–';
            el('stat-late-count').textContent = fmt(d.lateCount ?? 0);

            // Chart: Trend (Line)
            if (d.series?.labels && d.series?.values) {
                chartTrend?.destroy();
                chartTrend = new Chart(el('chart-trend'), {
                    type: 'line',
                    data: {
                        labels: d.series.labels,
                        datasets: [{
                            label: 'Hadir (Distinct User)',
                            data: d.series.values,
                            tension: .35,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }

            // Chart: Komposisi Hari Ini (Doughnut)
            chartToday?.destroy();
            chartToday = new Chart(el('chart-today'), {
                type: 'doughnut',
                data: {
                    labels: ['Clock In', 'Clock Out', 'In & Out'],
                    datasets: [{
                        data: [d.todayClockInOnly ?? 0, d.todayClockOutOnly ?? 0, d
                            .todayAttendanceBoth ?? 0
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            // Chart: per Lokasi (Bar)
            const locLabels = (d.byLocation ?? []).map(x => x.label);
            const locValues = (d.byLocation ?? []).map(x => x.value);
            chartLocation?.destroy();
            chartLocation = new Chart(el('chart-location'), {
                type: 'bar',
                data: {
                    labels: locLabels,
                    datasets: [{
                        label: 'Distinct User (bulan ini)',
                        data: locValues
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        // Filter & refresh
        el('apply-filter')?.addEventListener('click', () => {
            loadStats({
                from: el('from').value,
                to: el('to').value
            });
        });
        const refreshAll = () => {
            el('from').value = '';
            el('to').value = '';
            loadStats();
        };
        el('btn-refresh-stats')?.addEventListener('click', refreshAll);
        el('btn-refresh-stats-dd')?.addEventListener('click', refreshAll);
        el('btn-refresh-stats-dd-2')?.addEventListener('click', refreshAll);

        // Auto load awal
        loadStats();
    </script>
@endsection
