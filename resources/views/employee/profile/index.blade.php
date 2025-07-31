@extends('layouts.app')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4">My Profile</h4>

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2">
                        @include('employee.profile.partials.photo-upload', ['user' => $user])
                    </div>
                    <div class="col-md-10">
                        @include('employee.profile.partials.account-settings', ['user' => $user])
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    <script>
        document.getElementById('photo').addEventListener('change', function() {
            if (this.files && this.files[0]) {
                document.getElementById('photoUploadForm').submit();
            }
        });
    </script>
@endsection
