@extends('layouts.app')

@section('title', 'Order QR Code')

@section('content')
<div class="d-flex">
    @include('customer.sidebar')

    <div class="main-content flex-grow-1">
        <div class="page-header">
            <h4 class="page-title">QR Code Verification</h4>
            <a href="{{ route('customer.orders.show', $order->id) }}" class="btn btn-outline-success btn-sm px-3">
                <i class="fa fa-arrow-left me-2"></i>Back to Order
            </a>
        </div>

        <div class="content-area d-flex justify-content-center align-items-center" style="min-height: 70vh;">
            <div class="card border-0 shadow-sm rounded-3 p-5 text-center bg-white" style="max-width: 500px;">
                <h4 class="fw-bold mb-2 text-dark">{{ $order->business->business_name }}</h4>
                <p class="text-muted small mb-4">Present this QR code to the merchant to verify and claim your order.</p>

                @if($order->qrCode)
                    <div class="d-inline-block p-4 border rounded-3 bg-light mb-4 shadow-sm">
                        <img src="{{ asset($order->qrCode->qr_image_path) }}" alt="Order QR Code" class="img-fluid" style="max-width: 250px; height: 250px;">
                    </div>
                    <h5 class="fw-bold mb-3 text-dark">Code: {{ $order->reservation_code }}</h5>
                    <div>
                        <span class="badge {{ $order->qrCode->is_used ? 'bg-danger' : 'bg-success' }} py-2 px-4 fs-6">
                            {{ $order->qrCode->is_used ? 'Scanned & Used' : 'Active — Ready to Scan' }}
                        </span>
                    </div>
                @else
                    <div class="alert alert-warning py-3 mb-4 rounded-3">
                        <i class="fa fa-triangle-exclamation mb-2 fs-3 text-warning"></i>
                        <p class="mb-0 fw-semibold">No QR Code found for this order.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
