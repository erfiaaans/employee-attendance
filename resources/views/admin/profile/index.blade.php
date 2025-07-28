@extends('layouts.app')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4">
            <a href="#"><span class="text-muted fw-light">{{ __('Dashboards') }} /</span></a>
            <a href="#" class="text-secondary">{{ __('My Profile') }}</a>
        </h4>
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="card-body">
                            <div class="d-flex align-items-start align-items-sm-center gap-6 pb-4 border-bottom">
                                <img src="{{ asset('img/icons/user.png') }}" class="d-block w-px-100 h-px-100 rounded"
                                    id="uploadedAvatar" />
                                <div class="button-wrapper">
                                    <label for="upload" class="btn btn-primary me-3 mb-4" tabindex="0">
                                        <span class="d-none d-sm-block">Upload new photo</span>
                                        <i class="icon-base bx bx-upload d-block d-sm-none"></i>
                                        <input type="file" id="upload" class="account-file-input" hidden
                                            accept="image/png, image/jpeg" />
                                    </label>
                                    <button type="button" class="btn btn-outline-secondary account-image-reset mb-4">
                                        <i class="icon-base bx bx-reset d-block d-sm-none"></i>
                                        <span class="d-none d-sm-block">Reset</span>
                                    </button>

                                    <div>JPG, GIF or PNG. Max size of 800K</div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-4">
                            <form id="formAccountSettings" method="POST" action="{{ route('admin.user.store') }}"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="row g-6">
                                    <div class="col-md-6">
                                        <label for="Name" class="form-label">Name</label>
                                        <input class="form-control" type="text" id="name" name="name"
                                            value="{{ old('name', $user->name ?? '') }}" />
                                    </div>
                                    <div class="col-md-6">
                                        <label for="gender" class="form-label">Jenis Kelamin</label>
                                        <select id="gender" class="select2 form-select">
                                            <option value="">Pilih Jenis Kelamin</option>
                                            <option value="female">Perempuan</option>
                                            <option value="male">Laki-laki</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="position" class="form-label">Jabatan</label>
                                        <input class="form-control" value="{{ old('position', $user->position ?? '') }}"
                                            type="text" id="position" name="position" placeholder="Teknisi" />
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">E-mail</label>
                                        <input class="form-control" type="text" id="email" name="email"
                                            value="john.doe@example.com" placeholder="john.doe@example.com" />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="telephone">Telephone</label>
                                        <div class="input-group input-group-merge">
                                            <span class="input-group-text">ID (+62)</span>
                                            <input type="text" id="telephone" name="telephone" class="form-control"
                                                placeholder="202 555 0111" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="office-name" class="form-label">Kantor</label>
                                        <input class="form-control" type="text" id="office-name" name="office-name"
                                            placeholder="Onmeso Madiun" />
                                    </div>
                                </div>
                                <div class="mt-6">
                                    <button type="submit" class="btn btn-primary me-3">Save
                                        changes</button>
                                    <button type="reset" class="btn btn-outline-secondary">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
