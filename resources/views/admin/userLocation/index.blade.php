 @extends('layouts.app')
 @section('css')
     <style>
         td ul.list-unstyled>li:last-child {
             border-bottom: 0 !important;
         }

         td ul.list-unstyled>li .btn-group .btn {
             margin-left: .25rem;
         }
     </style>
 @endsection
 @section('content')
     <div class="container-xxl flex-grow-1 container-p-y">
         <h4 class="py-3 mb-4">
             <span class="text-muted fw-light">Dashboards /</span>
             <span class="text-secondary">Lokasi Pegawai</span>
         </h4>
         <div class="card mb-4" id="formCard">
             <div class="card-header d-flex justify-content-between align-items-center">
                 <h5 class="mb-0">{{ isset($editItem) ? 'Edit Lokasi Pegawai' : 'Tambah Lokasi Pegawai' }}</h5>
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
                 <div class="table-responsive">
                     <table id="datatable" class="table table-bordered table-hover align-middle">
                         <thead class="table-light">
                             <tr>
                                 <th style="width:56px;">No</th>
                                 <th>Nama Pegawai</th>
                                 <th style="width:90px;">Foto</th>
                                 <th>Jabatan</th>
                                 <th>Nama Kantor</th>
                             </tr>
                         </thead>
                         <tbody>
                             @forelse ($users as $i => $user)
                                 <tr>
                                     <td>{{ $i + 1 }}</td>
                                     <td>{{ $user->name }}</td>
                                     <td>
                                         @if (!empty($user->photo_url))
                                             <img src="{{ $user->photo_url }}" alt="Foto" width="60"
                                                 class="rounded">
                                         @else
                                             <span class="text-muted">-</span>
                                         @endif
                                     </td>
                                     <td>{{ $user->position ?: '-' }}</td>

                                     <td>
                                         @if ($user->locations && $user->locations->count())
                                             <ul class="list-unstyled mb-0">
                                                 @foreach ($user->locations as $loc)
                                                     @php
                                                         $pivotId =
                                                             optional($loc->pivot)->location_user_id ??
                                                             optional(
                                                                 ($user->officeLocationUsers ?? collect())->firstWhere(
                                                                     'location_id',
                                                                     $loc->location_id,
                                                                 ),
                                                             )->location_user_id;
                                                     @endphp
                                                     <li
                                                         class="d-flex justify-content-between align-items-center py-1 border-bottom">
                                                         <span class="me-3">{{ $loc->office_name }}</span>
                                                         <span class="btn-group">
                                                             <form
                                                                 action="{{ route('admin.userLocation.destroy', $pivotId) }}"
                                                                 method="POST" class="d-inline">
                                                                 @csrf
                                                                 @method('DELETE')
                                                                 <a href="{{ route('admin.userLocation.index', ['edit' => $pivotId]) }}"
                                                                     class="btn btn-icon btn-primary btn-sm" title="Edit">
                                                                     <span
                                                                         class="tf-icons bx bx-edit-alt text-white"></span>
                                                                 </a>
                                                                 <button type="submit"
                                                                     class="btn btn-icon btn-danger btn-sm swalDeleteData">
                                                                     <i class="tf-icons bx bx-trash text-white"></i>
                                                                 </button>
                                                             </form>
                                                         </span>
                                                     </li>
                                                 @endforeach
                                             </ul>
                                         @else
                                             <div class="d-flex justify-content-between align-items-center">
                                                 <span class="text-muted">-</span>
                                                 <a href="#" type="submit" class="btn btn-icon btn-info btn-sm ">
                                                     <i class="tf-icons bx bx-plus text-white"></i>
                                                 </a>
                                             </div>
                                         @endif
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

             </div>
         </div>
     </div>

 @endsection
