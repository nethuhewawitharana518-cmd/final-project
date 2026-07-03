@extends('layouts.app')

@section('title', 'Admin Settings')

@section('content')
<div class="d-flex">
    @include('admin.sidebar')

    <div class="main-content flex-grow-1">
        <div class="page-header">
            <h4 class="page-title">Global Settings</h4>
            <span class="badge bg-danger status-badge active">System Configuration</span>
        </div>

        <div class="content-area text-start" style="max-width: 700px;">
            <div class="card border-0 shadow-sm bg-white p-4 rounded-3">
                <h5 class="fw-bold mb-4 text-dark">Platform Settings</h5>
                <hr>
                
                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="commission_rate" class="form-label fw-semibold">Default Platform Commission Rate (%)</label>
                        <input type="number" step="0.1" name="commission_rate" id="commission_rate" class="form-control" value="10.0" required>
                        <small class="text-muted">The percentage cut the platform takes from each successful surplus food rescue transaction.</small>
                    </div>

                    <div class="mb-4">
                        <label for="registration_fee" class="form-label fw-semibold">Business Registration Verification Fee (Rs.)</label>
                        <input type="number" name="registration_fee" id="registration_fee" class="form-control" value="2000" required>
                        <small class="text-muted">One-time administrative processing fee charged to new hotel/bakery vendor registrations.</small>
                    </div>

                    <button type="submit" class="btn btn-success px-4 py-2">Save Settings</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
