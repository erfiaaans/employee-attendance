@extends('layouts.app')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="card md-12">
                <div class="col-mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ __('Daftar Lokasi Pegawai') }}</h5>
                    </div>
                    @include('admin.userLocation.create')
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.userLocation.index') }}">
                            <div class="row mb-2">
                                <div class="col-md-12">
                                    <div class="d-flex align-items-center flex-wrap gap-2">
                                        <input type="text" name="search" class="form-control me-2"
                                            style="max-width: 250px;" placeholder="Cari nama pegawai, jabatan, kantor..."
                                            value="{{ request('search') }}">
                                        <button type="submit" class="btn btn-light btn-sm">Cari</button>
                                        <div class="dropdown">
                                            <button class="btn btn-secondary btn-sm dropdown-toggle" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                Pilih Pegawai
                                            </button>
                                            <ul class="dropdown-menu">
                                                @foreach ($usersLocations as $userLocation)
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('admin.userLocation.index', ['search' => $userLocation->name]) }}">{{ $userLocation->name }}</a>
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
                                        <th>Foto</th>
                                        <th>Jabatan</th>
                                        <th>Nama Kantor</th>
                                        <th style="width: 110px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($usersLocations as $index => $userLocation)
                                        <tr>
                                            <td>{{ $index + $usersLocations->firstItem() + $index }}</td>
                                            <td>{{ $userLocation->user->name }}</td>
                                            <td>
                                                @if ($userLocation->user->photo_url)
                                                    <img src="{{ $userLocation->user->photo_url }}" alt="Profile"
                                                        width="60">
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{ $userLocation->user->position }}</td>
                                            <td>{{ $userLocation->locations->office_name }}</td>
                                            <td class="text-center">
                                                <div class="btn-group">
                                                    <form
                                                        action="{{ route('admin.userLocation.destroy', $userLocation->user_id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <a href="{{ route('admin.userLocation.edit', $userLocation->user_id) }}"
                                                            class="btn btn-icon btn-primary btn-sm">
                                                            <span class="tf-icons bx bx-edit-alt text-white"></span>
                                                        </a>
                                                        <button type="submit"
                                                            class="btn btn-icon btn-danger btn-sm swalDeleteData"><i
                                                                class="tf-icons bx bx-trash text-white"></i></button>
                                                    </form>
                                                </div>
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
                        <div class="my-4 px-3">
                            <nav aria-label="...">
                                <ul class="pagination">
                                    <li class="page-item {{ $usersLocations->onFirstPage() ? 'disabled' : '' }}">
                                        <a class="page-link"
                                            href="{{ $usersLocations->previousPageUrl() ?? '#' }}">Previous</a>
                                    </li>
                                    @for ($i = 1; $i <= $usersLocations->lastPage(); $i++)
                                        <li class="page-item {{ $i == $usersLocations->currentPage() ? 'active' : '' }}">
                                            <a class="page-link"
                                                href="{{ $usersLocations->url($i) }}">{{ $i }}</a>
                                        </li>
                                    @endfor
                                    <li class="page-item {{ $usersLocations->hasMorePages() ? '' : 'disabled' }}">
                                        <a class="page-link" href="{{ $usersLocations->nextPageUrl() ?? '#' }}">Next</a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-end">
            {{ $usersLocations->withQueryString()->links() }}
        </div>
    </div>
@endsection
