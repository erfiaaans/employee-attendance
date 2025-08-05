@extends('layouts.app')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4">
            <span class="text-muted fw-light">Dashboards /</span>
            <span class="text-secondary">Lokasi Pegawai</span>
        </h4>
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Tambah Lokasi Pegawai</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.userLocation.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="user_id" class="form-label">Pilih Pegawai</label>
                        <select name="user_id" id="user_id" class="form-select form-select-sm" required>
                            <option value="">-- Pilih Pegawai --</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->user_id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="location_id" class="form-label">Pilih Kantor</label>
                        <select name="location_id" id="location_id" class="form-select form-select-sm" required>
                            <option value="">-- Pilih Kantor --</option>
                            @foreach ($locations as $location)
                                <option value="{{ $location->location_id }}">{{ $location->office_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                </form>
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Daftar Lokasi Pegawai</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.userLocation.index') }}" class="mb-3">
                    <div class="row mb-2">
                        <div class="col-md-12">
                            <div class="d-flex align-items-center flex-wrap gap-2">
                                <input type="text" name="search" class="form-control me-2" style="max-width: 250px;"
                                    placeholder="Cari nama pegawai, jabatan, kantor..." value="{{ request('search') }}">
                                <button type="submit" class="btn btn-light">Cari</button>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
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
                                    <td>{{ $usersLocations->firstItem() + $index }}</td>
                                    <td>{{ $userLocation->user->name }}</td>
                                    <td>
                                        @if ($userLocation->user->photo_url)
                                            <img src="{{ $userLocation->user->photo_url }}" alt="Foto" width="60"
                                                class="rounded">
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $userLocation->user->position }}</td>
                                    <td>{{ $userLocation->locations->office_name }}</td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a href="{{ route('admin.userLocation.edit', $userLocation->location_user_id) }}"
                                                class="btn btn-icon btn-primary btn-sm" title="Edit">
                                                <i class="bx bx-edit-alt text-white"></i>
                                            </a>
                                            <form
                                                action="{{ route('admin.userLocation.destroy', $userLocation->location_user_id) }}"
                                                method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-icon btn-danger btn-sm"
                                                    title="Hapus">
                                                    <i class="bx bx-trash text-white"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Data tidak ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="my-4 px-3">
                    <nav aria-label="...">
                        <ul class="pagination">
                            <li class="page-item {{ $usersLocations->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $usersLocations->previousPageUrl() ?? '#' }}">Previous</a>
                            </li>
                            @for ($i = 1; $i <= $usersLocations->lastPage(); $i++)
                                <li class="page-item {{ $i == $usersLocations->currentPage() ? 'active' : '' }}">
                                    <a class="page-link" href="{{ $usersLocations->url($i) }}">{{ $i }}</a>
                                </li>
                            @endfor
                            <li class="page-item {{ $usersLocations->hasMorePages() ? '' : 'disabled' }}">
                                <a class="page-link" href="{{ $usersLocations->nextPageUrl() ?? '#' }}">Next</a>
                            </li>
                        </ul>
                    </nav>
                </div>
                <div class="mt-3 d-flex justify-content-end">
                    {{ $usersLocations->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
