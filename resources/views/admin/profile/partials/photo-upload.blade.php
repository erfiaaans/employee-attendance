<form id="photoUploadForm" method="POST" action="{{ route('admin.profile.upload_photo', $user->user_id) }}"
    enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="card-body">
        <div class="d-flex flex-column align-items-center justify-content-center gap-4 pb-4 border-bottom"
            style="min-height: 300px;">
            <img src="{{ $user->photo_url }}" class="d-block w-px-100 h-px-100 rounded" id="uploadedAvatar" />
            <input class="form-control d-none" type="file" id="photo" name="photo" accept="image/*">
            <div class="button-wrapper text-center">
                <label for="photo" class="btn btn-primary btn-sm mb-2" tabindex="0">
                    <span class="d-none d-sm-block">Change Photo</span>
                    <i class="icon-base bx bx-upload d-block d-sm-none"></i>
                </label>
            </div>
        </div>
    </div>
</form>
