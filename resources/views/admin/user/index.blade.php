@extends('layouts.app')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4">
            <a href="#"><span class="text-muted fw-light">{{ __('Dashboards') }} /</span></a>
            <a href="#" class="text-secondary">{{ __('Daftar Pegawai') }}</a>
        </h4>
        @include('admin.user.create')
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ __('List Data Pegawai') }}</h5>
                    </div>
                    <div class="card-body">
                        <form method="GET test" action="{{ route('admin.user') }}">
                            <div class="row mb-2">
                                <div class="col-md-12">
                                    <div class="d-flex align-items-center flex-wrap gap-2">
                                        <input type="text" name="search" class="form-control me-2"
                                            style="max-width: 250px;" placeholder="Cari nama pegawai, jabatan, kantor..."
                                            value="{{ request('search') }}">
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
                                        <th>Role</th>
                                        <th>Jabatan</th>
                                        <th>Jenis Kelamin</th>
                                        <th>Telepon</th>
                                        <th>Email</th>
                                        <th>Foto</th>
                                        <th style="width: 110px;">Aksi</th>
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
                                            <td>
                                                <img src="{{ $user->photo_url }}" alt="Foto" width="60"
                                                    class="rounded">
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group">
                                                    <form action="{{ route('admin.user.destroy', $user->user_id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <a href="{{ route('admin.user.edit', $user->user_id) }}"
                                                            class="btn btn-icon btn-primary btn-sm" title="Edit">
                                                            <span class="tf-icons bx bx-edit-alt text-white"></span>
                                                        </a>
                                                        <button type="submit"
                                                            class="btn btn-icon btn-danger btn-sm swalDeleteData"
                                                            title="Delete">
                                                            <span class="tf-icons bx bx-trash text-white"></span>
                                                        </button>
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
