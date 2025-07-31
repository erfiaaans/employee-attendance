@extends('layouts.app')
@push('styles')
    {{-- <link rel="stylesheet" href="{{ asset('css/dashboardStyle.css') }}"> --}}
@endpush
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4">
            <a href="#"><span class="text-muted fw-light">{{ __('Dashboards') }} /</span>
            </a>
            <a href="#" class="text-secondary">
                {{ __('Master User') }} /</a> {{ __('Detail') }}
        </h4>
        {{-- <div class="row">
            <h1>Data Dahboard</h1>
            <pre>{{ json_encode(Auth::user(), JSON_PRETTY_PRINT) }}</pre>
        </div> --}}
        <div class="col-12">
            <div class="row">
                <div class="col-12 col-md-6 col-xl-3 mb-4">
                    <a href="{{ route('employee.profile.index') }}" class="text-decoration-none text-dark">
                        <div class="card square-card">
                            <div class="card-body d-flex flex-column align-items-center justify-content-center">
                                <img src="{{ asset('img/icons/user.png') }}" alt="Profil" class="icon-img mb-2" />
                                <p class="mb-0 fw-bold">Profil</p>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-12 col-md-6 col-xl-3 mb-4">
                    <a href="{{ route('employee.clock_in') }}" class="text-decoration-none text-dark">
                        <div class="card square-card">
                            <div class="card-body d-flex flex-column align-items-center justify-content-center">
                                <img src="{{ asset('img/icons/clock_in.png') }}" alt="Office" class="icon-img mb-3" />
                                <p class="mb-0 fw-bold">Clock In</p>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-12 col-md-6 col-xl-3 mb-4">
                    <a href="{{ route('employee.clock_out') }}" class="text-decoration-none text-dark">
                        <div class="card square-card">
                            <div class="card-body d-flex flex-column align-items-center justify-content-center">
                                <img src="{{ asset('img/icons/clock_out.png') }}" alt="Daftar Pegawai"
                                    class="icon-img mb-3" />
                                <p class="mb-0 fw-bold">Clock Out</p>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-12 col-md-6 col-xl-3 mb-4">
                    <a href="{{ route('employee.attendance') }}" class="text-decoration-none text-dark">
                        <div class="card square-card">
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
    </div>
@endsection
