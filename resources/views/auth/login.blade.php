@extends('layouts.auth')

@section('content')
    <div class="container-fluid vh-100 d-flex align-items-center bg-light">
        <div class="row w-100 h-100 g-0">
            <!-- Left Image -->
            <div class="col-md-6 d-none d-md-block">
                <img src="{{ asset('img/logo-login.jpg') }}" alt="Welcome" class="img-fluid h-100 w-100"
                    style="object-fit: cover;" />
            </div>
            <!-- Right Login Form -->
            <div class="col-md-6 d-flex align-items-center justify-content-center px-4">
                <div style="width: 100%; max-width: 450px;">
                    {{-- <div class="text-center mb-4">Employee Attendance
                </div> --}}
                    <div class="card shadow-lg border-0 rounded-3 w-100">
                        <div class="card-body p-5">
                            <h2 class="mb-1 text-center">Welcome to Attend Guard! 👋</h2>
                            <p class="text-muted text-center mb-4">Please sign-in to your account!</p>

                            <form method="POST" action="{{ route('login') }}">
                                @csrf

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="text" class="form-control @error('email') is-invalid @enderror"
                                        id="email" name="email" value="{{ old('email') }}" autofocus
                                        placeholder="Enter your email " />
                                    @error('email')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                                            id="password" name="password" placeholder="************" />
                                        {{-- <span class="input-group-text">
                                    <i class="bx bx-hide"></i>
                                </span> --}}
                                        @error('password')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember"
                                {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">Remember Me</label>
                        </div> --}}

                                <button type="submit" class="btn btn-primary w-100">Login</button>

                                {{-- @if (Route::has('password.request'))
                            <div class="text-center mt-3">
                                <a href="{{ route('password.request') }}">Forgot Your Password?</a>
                            </div>
                        @endif --}}
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
