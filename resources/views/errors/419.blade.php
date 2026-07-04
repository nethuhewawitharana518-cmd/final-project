@extends('layouts.app')

@section('title', 'Session Expired')

@section('content')
<div class="d-flex align-items-center justify-content-center" style="min-height: 60vh;">
    <div class="text-center px-4" style="max-width: 500px;">
        <div class="mb-4">
            <i class="fa fa-clock text-warning" style="font-size: 4rem; opacity: 0.7;"></i>
        </div>
        <h2 class="fw-bold text-white mb-3">Session Expired</h2>
        <p class="text-muted mb-4">
            Your session or security token has expired. This usually happens when a page is left open for too long.
            Please refresh and try again.
        </p>
        <div class="d-flex gap-3 justify-content-center">
            <a href="javascript:history.back()" class="btn btn-outline-light px-4 py-2 rounded-pill">
                <i class="fa fa-arrow-left me-2"></i>Go Back
            </a>
            <a href="{{ url('/') }}" class="btn btn-success px-4 py-2 rounded-pill">
                <i class="fa fa-home me-2"></i>Home
            </a>
        </div>
    </div>
</div>
@endsection
