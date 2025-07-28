@extends('layouts.app')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="card md-12">
                <div class="col-mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ __('Daftar Pegawai') }}</h5>
                    </div>
                    @include('admin.user.create')
                    <div class="card-body">
                        <form method="GET test" action="{{ route('admin.user') }}">
                            <div class="row mb-2">
                                <div class="col-md-12">
                                    <div class="d-flex align-items-center flex-wrap gap-2">
                                        <input type="text" name="search" class="form-control me-2"
                                            style="max-width: 250px;" placeholder="Cari nama pegawai, jabatan, kantor..."
                                            value="{{ request('search') }}">
                                        <button type="submit" class="btn btn-light btn-sm">Cari</button>
                                        <select name="filter" class="form-select form-select-sm" style="max-width: 150px;"
                                            onchange="this.form.submit()">
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
                                        <th>Role</th>
                                        <th>Jabatan</th>
                                        <th>Jenis Kelamin</th>
                                        <th>Telepon</th>
                                        <th>Email</th>
                                        <th>Foto</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($users as $index => $user)
                                        <tr>
                                            <td>{{ $index + $users->firstItem() }}</td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->role }}</td>
                                            <td>{{ $user->position }}</td>
                                            <td>{{ $user->gender }}</td>
                                            <td>{{ $user->telephone }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->profile_picture_url }}</td>
                                            <td class="text-center">
                                                <div class="btn-group">
                                                    <form action="{{ route('admin.user.destroy', $user->user_id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <a href="{{ route('admin.user.edit', $user->user_id) }}"
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
                                    <li class="page-item {{ $users->onFirstPage() ? 'disabled' : '' }}">
                                        <a class="page-link" href="{{ $users->previousPageUrl() ?? '#' }}">Previous</a>
                                    </li>
                                    @for ($i = 1; $i <= $users->lastPage(); $i++)
                                        <li class="page-item {{ $i == $users->currentPage() ? 'active' : '' }}">
                                            <a class="page-link" href="{{ $users->url($i) }}">{{ $i }}</a>
                                        </li>
                                    @endfor
                                    <li class="page-item {{ $users->hasMorePages() ? '' : 'disabled' }}">
                                        <a class="page-link" href="{{ $users->nextPageUrl() ?? '#' }}">Next</a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-end">
            {{ $users->withQueryString()->links() }}
        </div>
    </div>
@endsection
