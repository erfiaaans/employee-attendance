 @extends('layouts.app')
 @section('content')
     <div class="container-xxl flex-grow-1 container-p-y">
         <h4 class="py-3 mb-4">
             <span class="text-muted fw-light">Dashboards /</span>
             <span class="text-secondary">Lokasi Pegawai</span>
         </h4>
         <div class="card mb-4" id="formCard">
             <div class="card-header d-flex justify-content-between align-items-center">
                 <h5 class="mb-0">{{ isset($editItem) ? 'Edit Lokasi Pegawai' : 'Tambah Lokasi Pegawai' }}</h5>
                 {{-- @if (isset($editItem))
                     <a href="{{ route('admin.userLocation.index', request()->except('edit')) }}"
                         class="btn btn-sm btn-secondary">Batal Edit</a>
                 @endif --}}
             </div>
             <div class="card-body">
                 @if (isset($editItem))
                     <form action="{{ route('admin.userLocation.update', $editItem->location_user_id) }}" method="POST"
                         class="row g-3">
                         @method('PUT')
                     @else
                         <form action="{{ route('admin.userLocation.store') }}" method="POST" class="row g-3">
                 @endif
                 @csrf
                 <div class="col-md-6">
                     <label for="user_id" class="form-label">Pilih Pegawai</label>
                     <select name="user_id" id="user_id" class="form-select form-select-sm" required>
                         <option value="">-- Pilih Pegawai --</option>
                         @foreach ($users as $user)
                             <option value="{{ $user->user_id }}"
                                 {{ old('user_id', $editItem->user_id ?? '') == $user->user_id ? 'selected' : '' }}>
                                 {{ $user->name }}{{ $user->position ? ' — ' . $user->position : '' }}
                             </option>
                         @endforeach
                     </select>
                     @error('user_id')
                         <small class="text-danger">{{ $message }}</small>
                     @enderror
                 </div>
                 <div class="col-md-6">
                     <label for="location_id" class="form-label">Pilih Kantor</label>
                     <select name="location_id" id="location_id" class="form-select form-select-sm" required>
                         <option value="">-- Pilih Kantor --</option>
                         @foreach ($locations as $location)
                             <option value="{{ $location->location_id }}"
                                 {{ old('location_id', $editItem->location_id ?? '') == $location->location_id ? 'selected' : '' }}>
                                 {{ $location->office_name }}
                             </option>
                         @endforeach
                     </select>
                     @error('location_id')
                         <small class="text-danger">{{ $message }}</small>
                     @enderror
                 </div>
                 <div class="col-12 d-flex gap-2">
                     <button type="submit" class="btn btn-primary btn-sm">
                         {{ isset($editItem) ? 'Update' : 'Simpan' }}
                     </button>
                     @if (isset($editItem))
                         <a href="{{ route('admin.userLocation.index', request()->except('edit')) }}"
                             class="btn btn-light btn-sm">Batal</a>
                     @endif
                 </div>
                 </form>
             </div>
         </div>
         <div class="card mb-4">
             <div class="card-header">
                 <h5 class="mb-0">Daftar Lokasi Pegawai</h5>
             </div>
             <div class="card-body">
                 <form method="GET" action="{{ route('admin.userLocation.index') }}" class="mb-3">
                     <div class="d-flex align-items-center flex-wrap gap-2">
                         <input type="text" name="search" class="form-control form-control-sm" style="max-width: 320px;"
                             placeholder="Cari nama pegawai, jabatan, kantor..." value="{{ request('search') }}">
                         <button type="submit" class="btn btn-light btn-sm">Cari</button>
                         @if (request('search'))
                             <a class="btn btn-outline-secondary btn-sm"
                                 href="{{ route('admin.userLocation.index') }}">Reset</a>
                         @endif
                     </div>
                 </form>
                 <div class="table-responsive">
                     <table class="table table-bordered table-hover align-middle">
                         <thead class="table-light">
                             <tr>
                                 <th style="width:56px;">No</th>
                                 <th>Nama Pegawai</th>
                                 <th style="width:90px;">Foto</th>
                                 <th>Jabatan</th>
                                 <th>Nama Kantor</th>
                                 <th style="width:130px;">Aksi</th>
                             </tr>
                         </thead>
                         <tbody>
                             @forelse ($usersLocations as $i => $row)
                                 <tr>
                                     <td>{{ $usersLocations->firstItem() + $i }}</td>
                                     <td>{{ $row->user->name }}</td>
                                     <td>
                                         @if ($row->user->photo_url)
                                             <img src="{{ $row->user->photo_url }}" alt="Foto" width="60"
                                                 class="rounded">
                                         @else
                                             <span class="text-muted">-</span>
                                         @endif
                                     </td>
                                     <td>{{ $row->user->position ?: '-' }}</td>
                                     <td>{{ $row->locations->office_name }}</td>
                                     <td class="text-center">
                                         <div class="btn-group">
                                             <form
                                                 action="{{ route('admin.userLocation.destroy', $row->location_user_id) }}"
                                                 method="POST">
                                                 @csrf
                                                 @method('DELETE')
                                                 <a href="{{ route('admin.userLocation.index', array_merge(request()->only('search', 'page'), ['edit' => $row->location_user_id])) }}"
                                                     class="btn btn-icon btn-primary btn-sm" title="Edit">
                                                     <span class="tf-icons bx bx-edit-alt text-white"></span>
                                                 </a>
                                                 <button type="submit"
                                                     class="btn btn-icon btn-danger btn-sm swalDeleteData">
                                                     <i class="tf-icons bx bx-trash text-white"></i>
                                                 </button>
                                             </form>
                                         </div>

                                     </td>
                                 </tr>
                             @empty
                                 <tr>
                                     <td colspan="6" class="text-center text-muted">Data tidak ditemukan.</td>
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
                 @if ($usersLocations->hasPages())
                     <div class="mt-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                         <div class="small text-muted">
                             Menampilkan {{ $usersLocations->firstItem() }}–{{ $usersLocations->lastItem() }} dari
                             {{ $usersLocations->total() }} entri
                         </div>
                         <div>
                             {{ $usersLocations->withQueryString()->links() }}
                         </div>
                     </div>
                 @endif
             </div>
         </div>
     </div>
     @if (isset($editItem))
         <script>
             window.addEventListener('load', () => {
                 const el = document.getElementById('formCard');
                 if (el) el.scrollIntoView({
                     behavior: 'smooth',
                     block: 'start'
                 });
             });
         </script>
     @endif
 @endsection
