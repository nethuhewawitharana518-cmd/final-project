@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="d-flex">
    @include('customer.sidebar')

    <div class="main-content flex-grow-1">
        <div class="page-header">
            <h4 class="page-title">My Profile</h4>
            <span class="badge bg-success status-badge active">Account Settings</span>
        </div>

        <div class="content-area">
            <div class="row g-4">
                {{-- Profile Info Card --}}
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm rounded-3 p-4 bg-white">
                        <h5 class="fw-bold text-dark mb-4">Profile Information</h5>
                        <form action="{{ route('customer.profile.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', auth()->user()->name) }}" required>
                                <div class="text-danger small mt-1 d-none" id="name-warning">⚠️ Please enter letters and spaces only. Numbers or special characters are not allowed.</div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" value="{{ auth()->user()->email }}" disabled>
                                <span class="text-muted small">Email cannot be changed.</span>
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', auth()->user()->phone) }}" maxlength="10" placeholder="e.g., 0771234567" required>
                                <div class="text-danger small mt-1 d-none" id="phone-warning">⚠️ Please enter exactly 10 digits starting with 07 (e.g., 0771234567). Only numbers are allowed.</div>
                            </div>

                            <div class="mb-3">
                                <label for="home_address" class="form-label">Home Address</label>
                                <textarea class="form-control" id="home_address" name="home_address" rows="2" required>{{ old('home_address', auth()->user()->home_address) }}</textarea>
                            </div>

                            <div class="mb-4">
                                <label for="avatar" class="form-label">Profile Picture</label>
                                <input type="file" class="form-control" id="avatar" name="avatar">
                            </div>

                            <button type="submit" class="btn btn-success px-4">Save Changes</button>
                        </form>
                    </div>
                </div>

                {{-- Password Update Card --}}
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm rounded-3 p-4 bg-white">
                        <h5 class="fw-bold text-dark mb-4">Change Password</h5>
                        <form action="{{ route('customer.profile.password') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="current_password" class="form-label">Current Password</label>
                                <input type="password" class="form-control" id="current_password" name="current_password" required>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>

                            <div class="mb-4">
                                <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                            </div>

                            <button type="submit" class="btn btn-success px-4">Update Password</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const nameInput = document.getElementById('name');
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
});
</script>
@endpush
