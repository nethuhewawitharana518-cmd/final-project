@extends('layouts.app')

@section('title', 'Customer Signup')
@section('meta_description', 'Create a customer account on FoodRescue to reserve discounted food deals in Trincomalee.')

@push('styles')
<style>
    .input-group button.input-group-text {
        outline: none;
        box-shadow: none;
        cursor: pointer;
        transition: color 0.2s;
    }
    .input-group button.input-group-text:hover {
        color: var(--primary) !important;
    }
</style>
@endpush

@section('content')
<section class="py-5 bg-light-gradient min-vh-80 d-flex align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card border-0 shadow-lg p-4 p-md-5 bg-white rounded-3">
                    <div class="text-center mb-4">
                        <div class="fs-1 mb-2">😋</div>
                        <h3 class="fw-bold text-dark mb-1">Customer Registration</h3>
                        <p class="text-muted small">Sign up to rescue food deals and earn loyalty points</p>
                    </div>

                    <form action="{{ route('register.customer.post') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="name" class="form-label text-dark small fw-semibold">Full Name</label>
                                <div class="input-group shadow-sm">
                                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa fa-user"></i></span>
                                    <input type="text" name="name" id="name" class="form-control border-start-0 ps-0 @error('name') is-invalid @enderror" 
                                        placeholder="Enter your full name..." value="{{ old('name') }}" required autofocus>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="text-danger small mt-1 d-none" id="name-warning">⚠️ Please enter letters and spaces only. Numbers or special characters are not allowed.</div>
                            </div>

                            <div class="col-12">
                                <label for="email" class="form-label text-dark small fw-semibold">Email Address</label>
                                <div class="input-group shadow-sm">
                                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa fa-envelope"></i></span>
                                    <input type="email" name="email" id="email" class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror" 
                                        placeholder="e.g., name@example.com" value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="phone" class="form-label text-dark small fw-semibold">Phone Number</label>
                                <div class="input-group shadow-sm">
                                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa fa-phone"></i></span>
                                    <input type="tel" name="phone" id="phone" class="form-control border-start-0 ps-0 @error('phone') is-invalid @enderror" 
                                        placeholder="e.g., 0771234567" value="{{ old('phone') }}" required maxlength="10" pattern="^07[0-9]{8}$"
                                        oninvalid="this.setCustomValidity('Please enter a valid 10-digit Sri Lankan phone number starting with 07.')"
                                        oninput="this.setCustomValidity('')">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="text-danger small mt-1 d-none" id="phone-warning">⚠️ Please enter exactly 10 digits starting with 07 (e.g., 0771234567). Only numbers are allowed.</div>
                            </div>

                            <div class="col-12">
                                <label for="home_address" class="form-label text-dark small fw-semibold">Home Address</label>
                                <div class="input-group shadow-sm">
                                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa fa-map-marker-alt"></i></span>
                                    <textarea name="home_address" id="home_address" class="form-control border-start-0 ps-0 @error('home_address') is-invalid @enderror" 
                                        placeholder="e.g., No. 123, Kandy Road, Trincomalee." rows="2" required>{{ old('home_address') }}</textarea>
                                    @error('home_address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="password" class="form-label text-dark small fw-semibold">Password</label>
                                <div class="input-group shadow-sm">
                                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa fa-lock"></i></span>
                                    <input type="password" name="password" id="password" class="form-control border-start-0 border-end-0 ps-0 @error('password') is-invalid @enderror" 
                                        placeholder="Min 8 characters" required>
                                    <button class="input-group-text bg-white border-start-0 text-muted" type="button" id="toggle-password">
                                        <i class="fa fa-eye" id="password-eye"></i>
                                    </button>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label text-dark small fw-semibold">Confirm Password</label>
                                <div class="input-group shadow-sm">
                                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa fa-lock"></i></span>
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control border-start-0 border-end-0 ps-0" 
                                        placeholder="Re-enter password" required>
                                    <button class="input-group-text bg-white border-start-0 text-muted" type="button" id="toggle-password-confirm">
                                        <i class="fa fa-eye" id="password-confirm-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-success w-100 rounded-pill fw-semibold py-3 shadow-sm">
                                    <i class="fa fa-user-plus me-2"></i>Sign Up
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="text-center mt-4">
                        <span class="text-muted small">Already have an account? </span>
                        <a href="{{ route('login') }}" class="text-success small fw-semibold text-decoration-none">Sign In Here</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const phoneInput = document.getElementById('phone');

    if (nameInput) {
        const nameWarning = document.getElementById('name-warning');
        nameInput.addEventListener('input', function () {
            let val = this.value.replace(/[^a-zA-Z\s]/g, '');
            if (this.value !== val) {
                if (nameWarning) nameWarning.classList.remove('d-none');
            } else {
                if (nameWarning && val.trim().length > 0) nameWarning.classList.add('d-none');
            }
            this.value = val;
        });
        nameInput.addEventListener('blur', function () {
            if (this.value.trim().length === 0 || /[^a-zA-Z\s]/.test(this.value)) {
                if (nameWarning) nameWarning.classList.remove('d-none');
            } else {
                if (nameWarning) nameWarning.classList.add('d-none');
            }
        });
    }

    if (emailInput) {
        // Email field: convert to lowercase
        emailInput.addEventListener('input', function () {
            this.value = this.value.toLowerCase();
        });
    }

    if (phoneInput) {
        const warning = document.getElementById('phone-warning');
        phoneInput.addEventListener('input', function () {
            let val = this.value.replace(/[^0-9]/g, '');
            if (this.value !== val) {
                if (warning) warning.classList.remove('d-none');
            } else {
                if (warning && val.length === 10 && val.startsWith('07')) warning.classList.add('d-none');
            }
            if (val.length > 0 && val[0] !== '0') {
                val = '';
                if (warning) warning.classList.remove('d-none');
            }
            if (val.length > 1 && val[1] !== '7') {
                val = '0';
                if (warning) warning.classList.remove('d-none');
            }
            this.value = val;
        });
        phoneInput.addEventListener('blur', function () {
            if (this.value.length < 10 || !this.value.startsWith('07')) {
                if (warning) warning.classList.remove('d-none');
            } else {
                if (warning) warning.classList.add('d-none');
            }
        });
    }

    const togglePasswordBtn = document.getElementById('toggle-password');
    const passwordInput = document.getElementById('password');
    const passwordEye = document.getElementById('password-eye');

    if (togglePasswordBtn && passwordInput && passwordEye) {
        togglePasswordBtn.addEventListener('click', function () {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            passwordEye.classList.toggle('fa-eye');
            passwordEye.classList.toggle('fa-eye-slash');
        });
    }

    const toggleConfirmBtn = document.getElementById('toggle-password-confirm');
    const confirmInput = document.getElementById('password_confirmation');
    const confirmEye = document.getElementById('password-confirm-eye');

    if (toggleConfirmBtn && confirmInput && confirmEye) {
        toggleConfirmBtn.addEventListener('click', function () {
            const type = confirmInput.getAttribute('type') === 'password' ? 'text' : 'password';
            confirmInput.setAttribute('type', type);
            confirmEye.classList.toggle('fa-eye');
            confirmEye.classList.toggle('fa-eye-slash');
        });
    }
});
</script>
@endpush
