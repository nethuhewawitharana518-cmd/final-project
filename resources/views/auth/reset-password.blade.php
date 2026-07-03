@extends('layouts.app')

@section('title', 'Reset Password')

@section('content')
<style>
    @keyframes slowPan {
        0% { transform: scale(1) translate(0, 0); }
        50% { transform: scale(1.1) translate(-2%, -2%); }
        100% { transform: scale(1) translate(0, 0); }
    }
    .cinematic-bg {
        position: absolute;
        width: 105%;
        height: 105%;
        object-fit: cover;
        top: -2.5%;
        left: -2.5%;
        z-index: 0;
        animation: slowPan 40s ease-in-out infinite;
    }
</style>

<section class="min-vh-100 d-flex align-items-center position-relative overflow-hidden py-5">
    <!-- Cinematic Slow-Motion Background -->
    <img src="{{ asset('assets/images/impact_bg.png') }}" class="cinematic-bg" alt="Cinematic Background">
    
    <!-- Dark Overlay 75% Opacity -->
    <div class="position-absolute w-100 h-100" style="background: rgba(18, 18, 18, 0.75); top: 0; left: 0; z-index: 1;"></div>
    
    <!-- Form Container -->
    <div class="container position-relative" style="z-index: 2;">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card p-4 p-md-5 rounded-3" style="background: rgba(30, 30, 30, 0.85); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.1); box-shadow: 0 25px 50px -12px rgba(0,0,0,0.7);">
                    <div class="text-center mb-4">
                        <div class="fs-1 mb-2">🔒</div>
                        <h3 class="fw-bold mb-1" style="color: #fff;">New Password</h3>
                        <p class="text-muted small">Please enter your new password below</p>
                    </div>

                    <form action="{{ route('password.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="mb-3">
                            <label for="email" class="form-label text-dark small fw-semibold">Email Address</label>
                            <div class="input-group shadow-sm">
                                <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa fa-envelope"></i></span>
                                <input type="email" name="email" id="email" class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror" 
                                    placeholder="yourname@example.com" value="{{ old('email') }}" required autofocus>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label text-dark small fw-semibold">New Password</label>
                            <div class="input-group shadow-sm">
                                <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa fa-lock"></i></span>
                                <input type="password" name="password" id="password" class="form-control border-start-0 ps-0 @error('password') is-invalid @enderror" 
                                    placeholder="Min 6 characters" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label text-dark small fw-semibold">Confirm Password</label>
                            <div class="input-group shadow-sm">
                                <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa fa-lock"></i></span>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control border-start-0 ps-0" 
                                    placeholder="Repeat new password" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 rounded-pill fw-semibold py-3 shadow-sm mb-3">
                            <i class="fa fa-check me-2"></i>Reset Password
                        </button>
                    </form>

                    <div class="text-center mt-3">
                        <a href="{{ route('login') }}" class="text-primary small fw-semibold text-decoration-none">Back to Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
