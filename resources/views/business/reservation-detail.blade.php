@extends('layouts.app')

@section('title', 'Reservation Details')

@section('content')
<div class="d-flex">
    @include('business.sidebar')

    <div class="main-content flex-grow-1">
        <div class="page-header">
            <h4 class="page-title">Booking details: #{{ $reservation->reservation_code }}</h4>
            <a href="{{ route('business.reservations') }}" class="btn btn-outline-danger btn-sm px-3">Back to List</a>
        </div>

        <div class="content-area text-start">
            <div class="row g-4">
                {{-- Order Summary --}}
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm p-4 bg-white mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                            <div>
                                <h5 class="fw-bold mb-1 text-dark">Surplus Food Reservation</h5>
                                <span class="text-muted small">Date: {{ $reservation->created_at->format('M d, Y H:i') }}</span>
                            </div>
                            <span class="status-badge {{ $reservation->status }} px-3 py-2 fs-6">
                                {{ ucfirst($reservation->status) }}
                            </span>
                        </div>

                        {{-- Customer & Fulfillment details --}}
                        @php
                            $isDelivery = $reservation->delivery_method === 'delivery';
                            $deliveryAddress = $isDelivery ? $reservation->delivery_address : '';
                        @endphp
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <h6 class="fw-bold text-muted text-uppercase small mb-2">Customer Info</h6>
                                <div class="p-3 border rounded-3 bg-light h-100">
                                    <h6 class="fw-bold mb-1 text-dark">{{ $reservation->customer->name ?? 'Guest User' }}</h6>
                                    <p class="text-muted small mb-0"><i class="fa fa-envelope me-2"></i>{{ $reservation->customer->email ?? 'N/A' }}</p>
                                    <p class="text-muted small mb-0"><i class="fa fa-phone me-2"></i>{{ $reservation->customer->phone ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-bold text-muted text-uppercase small mb-2">Fulfillment Details</h6>
                                <div class="p-3 border rounded-3 bg-light h-100">
                                    @if($isDelivery)
                                        <h6 class="fw-bold mb-1 text-dark"><i class="fa-solid fa-truck text-success me-2"></i>Home Delivery</h6>
                                        <p class="text-muted small mb-0"><strong>Time:</strong> {{ $reservation->pickup_time->format('M d, Y H:i') }}</p>
                                        <p class="text-muted small mb-0"><strong>Address:</strong> {{ $deliveryAddress }}</p>
                                    @else
                                        <h6 class="fw-bold mb-1 text-dark"><i class="fa-solid fa-store text-success me-2"></i>Self Pickup</h6>
                                        <p class="text-muted small mb-0"><strong>Time:</strong> {{ $reservation->pickup_time->format('M d, Y H:i') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Itemized List --}}
                        <div class="mb-4">
                            <h6 class="fw-bold text-muted text-uppercase small mb-2">Reserved Items</h6>
                            <div class="table-responsive border rounded-3">
                                <table class="table mb-0 align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-3">Item</th>
                                            <th>Rescue Price</th>
                                            <th>Qty</th>
                                            <th class="text-end pe-3">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($reservation->items as $item)
                                        <tr>
                                            <td class="ps-3 fw-semibold text-dark">{{ $item->food->name ?? $item->food_name }}</td>
                                            <td class="text-success fw-bold">Rs. {{ number_format($item->unit_price, 2) }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td class="text-end pe-3 fw-bold">Rs. {{ number_format($item->total_price, 2) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Total Calculation --}}
                        <div class="d-flex justify-content-end pt-3 border-top">
                            <div class="text-end" style="min-width: 250px;">
                                <div class="d-flex justify-content-between mb-2 small text-muted">
                                    <span>Subtotal:</span>
                                    <span class="fw-semibold text-dark">Rs. {{ number_format($reservation->subtotal, 2) }}</span>
                                </div>
                                @if($reservation->loyalty_discount > 0)
                                    <div class="d-flex justify-content-between mb-2 small text-muted">
                                        <span>Loyalty Discount:</span>
                                        <span class="fw-semibold text-danger">- Rs. {{ number_format($reservation->loyalty_discount, 2) }}</span>
                                    </div>
                                @endif
                                @if($isDelivery)
                                    <div class="d-flex justify-content-between mb-2 small text-muted">
                                        <span>Delivery Fee:</span>
                                        <span class="fw-semibold text-dark">Rs. {{ number_format($reservation->delivery_fee, 2) }}</span>
                                    </div>
                                @endif
                                <div class="d-flex justify-content-between border-top pt-2 fs-5 fw-bold">
                                    <span class="text-dark">Total Earnings:</span>
                                    <span class="text-success">Rs. {{ number_format($reservation->total_amount, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- QR code side --}}
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm p-4 bg-white mb-4 text-center">
                        <h5 class="fw-bold mb-3 text-dark">QR Code Status</h5>
                        @if($reservation->qrCode)
                            <div class="d-inline-block p-3 border rounded-3 bg-white mb-4 shadow-sm">
                                <img src="{{ asset($reservation->qrCode->qr_image_path) }}" alt="Order QR Code" class="img-fluid" style="max-width: 200px;">
                            </div>
                            <div class="mb-4">
                                <span class="badge {{ $reservation->qrCode->is_used ? 'bg-danger' : 'bg-success' }} py-2 px-3">
                                    {{ $reservation->qrCode->is_used ? 'Verified & Scanned' : 'Pending Verification' }}
                                </span>
                            </div>
                        @else
                            <div class="alert alert-warning py-3 mb-0 small rounded-3">
                                <p class="mb-0">No QR Code generated yet.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
