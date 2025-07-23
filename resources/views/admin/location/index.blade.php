@extends('layouts.app')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4">
            <a href="#"><span class="text-muted fw-light">{{ __('Dashboards') }} /</span></a>
            <a href="#" class="text-secondary">{{ __('Master Location') }} /</a>
        </h4>

        <div class="card">
            <div class="d-flex justify-content-between align-items-center my-4 px-3">
                <h5 class="card-header p-0 border-0">Lokasi Kantor Onmeso</h5>

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
            </div>

            @include('admin.location.modals.create')

            {{-- Form Pencarian --}}
            <div class="px-3 mb-3">
                <form method="GET" action="{{ route('admin.location') }}" class="d-flex">
                    <input type="text" name="search" class="form-control me-2"
                        placeholder="Cari nama kantor, alamat, kota..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-secondary">Cari</button>
                </form>
            </div>

            {{-- Tabel --}}
            <div class="table-responsive text-nowrap">
                <table class="table">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Kantor</th>
                            <th>Alamat</th>
                            <th>Kota</th>
                            <th>Latitude</th>
                            <th>Longitude</th>
                            <th>Radius</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse ($locations as $index => $location)
                            <tr>
                                <td>{{ $index + $locations->firstItem() }}</td>
                                <td>{{ $location->office_name }}</td>
                                <td>{{ $location->address }}</td>
                                <td>{{ $location->city }}</td>
                                <td>{{ $location->latitude }}</td>
                                <td>{{ $location->longitude }}</td>
                                <td>{{ $location->radius }}</td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <form action="{{ route('admin.location.destroy', $location->location_id) }}"
                                            method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <a href="{{ route('admin.location.edit', $location->location_id) }}"
                                                class="btn btn-icon btn-outline-primary btn-sm">
                                                <span class="tf-icons bx bx-edit-alt"></span>
                                            </a>
                                            <button type="submit"
                                                class="btn btn-icon btn-outline-danger btn-sm swalSuccesInActive"><i
                                                    class="tf-icons bx bx-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">Data tidak tersedia</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="my-4 px-3">
                <nav aria-label="...">
                    <ul class="pagination">
                        <li class="page-item {{ $locations->onFirstPage() ? 'disabled' : '' }}">
                            <a class="page-link" href="{{ $locations->previousPageUrl() ?? '#' }}">Previous</a>
                        </li>
                        @for ($i = 1; $i <= $locations->lastPage(); $i++)
                            <li class="page-item {{ $i == $locations->currentPage() ? 'active' : '' }}">
                                <a class="page-link" href="{{ $locations->url($i) }}">{{ $i }}</a>
                            </li>
                        @endfor
                        <li class="page-item {{ $locations->hasMorePages() ? '' : 'disabled' }}">
                            <a class="page-link" href="{{ $locations->nextPageUrl() ?? '#' }}">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
@endsection
