@extends('layouts.app')

@section('title', 'Payment Failed')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 text-center">

            <div class="card border-0 shadow-sm rounded-4 p-5">
                <div class="mb-4">
                    <div style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,#fee2e2,#fecaca);display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
                        <i class="fa fa-xmark text-danger" style="font-size:2.5rem;"></i>
                    </div>
                    <h3 class="fw-bold text-dark mb-2">Payment Failed</h3>
                    <p class="text-muted">{{ $reason }}</p>
                </div>

                <div class="alert alert-warning rounded-3 text-start mb-4">
                    <strong class="d-block mb-1"><i class="fa fa-info-circle me-1"></i>No charge was made</strong>
                    <small>Your card was not charged. You can try again with the same or a different card.</small>
                </div>

                <div class="d-flex flex-column gap-3">
                    <a href="{{ route('customer.checkout') }}" class="btn btn-success rounded-pill py-3 fw-bold">
                        <i class="fa fa-rotate-right me-2"></i>Try Payment Again
                    </a>
                    <a href="{{ route('customer.cart') }}" class="btn btn-outline-secondary rounded-pill">
                        <i class="fa fa-cart-shopping me-2"></i>Back to Cart
                    </a>
                </div>

                <div class="mt-4 text-muted small">
                    <i class="fa fa-headset me-1"></i>
                    Need help? <a href="{{ route('contact') }}" class="text-success">Contact support</a>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
