@extends('layouts.app')

@section('title', 'Customer Dashboard')

@section('content')
<div class="d-flex">
    @include('customer.sidebar')

    <div class="main-content flex-grow-1">
        <div class="page-header">
            <h4 class="page-title">Welcome back, {{ auth()->user()->name }}!</h4>
            <span class="status-badge active">Customer Account</span>
        </div>

        <div class="content-area">
            {{-- KPI Summary Cards --}}
            <div class="row g-4 mb-5">
                {{-- Active Orders Card --}}
                <div class="col-md-3 col-sm-6">
                    <div class="p-4 rounded-4 d-flex flex-column" style="background-color: #0c1421; border: 1px solid rgba(56, 189, 248, 0.1); height: 100%;">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-3 mb-3" style="width: 42px; height: 42px; background-color: rgba(56, 189, 248, 0.15); color: #38bdf8; font-size: 1.15rem;">
                            <i class="fa fa-shopping-bag"></i>
                        </div>
                        <h3 class="fw-bold text-white mb-1">{{ $activeOrdersCount }}</h3>
                        <div class="text-white-50 small mb-4">active orders</div>
                        <div class="small fw-semibold mt-auto" style="color: #38bdf8;">ongoing / current</div>
                    </div>
                </div>

                {{-- Eco / Sustainable Card --}}
                <div class="col-md-3 col-sm-6">
                    <div class="p-4 rounded-4 d-flex flex-column" style="background-color: #0d1a15; border: 1px solid rgba(74, 222, 128, 0.1); height: 100%;">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-3 mb-3" style="width: 42px; height: 42px; background-color: rgba(74, 222, 128, 0.15); color: #4ade80; font-size: 1.15rem;">
                            <i class="fa fa-leaf"></i>
                        </div>
                        <h3 class="fw-bold text-white mb-1">{{ number_format($co2Saved, 1) }} kg</h3>
                        <div class="text-white-50 small mb-4">CO₂ offset</div>
                        <div class="small fw-semibold mt-auto" style="color: #4ade80;">eco / sustainable</div>
                    </div>
                </div>

                {{-- Loyalty / Premium Card --}}
                <div class="col-md-3 col-sm-6">
                    <div class="p-4 rounded-4 d-flex flex-column" style="background-color: #0b1121; border: 1px solid rgba(250, 204, 21, 0.1); height: 100%;">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-3 mb-3" style="width: 42px; height: 42px; background-color: rgba(250, 204, 21, 0.15); color: #facc15; font-size: 1.15rem;">
                            <i class="fa fa-star"></i>
                        </div>
                        <h3 class="fw-bold text-white mb-1">{{ $loyaltyPoints }}</h3>
                        <div class="text-white-50 small mb-4">loyalty points</div>
                        <div class="small fw-semibold mt-auto" style="color: #facc15;">navy / premium</div>
                    </div>
                </div>

                {{-- Savings / Playful Card --}}
                <div class="col-md-3 col-sm-6">
                    <div class="p-4 rounded-4 d-flex flex-column" style="background-color: #1a0f14; border: 1px solid rgba(251, 113, 133, 0.1); height: 100%;">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-3 mb-3" style="width: 42px; height: 42px; background-color: rgba(251, 113, 133, 0.15); color: #fb7185; font-size: 1.15rem;">
                            <i class="fa fa-hand-rock"></i>
                        </div>
                        <h3 class="fw-bold text-white mb-1">Rs. {{ number_format($savedAmount) }}</h3>
                        <div class="text-white-50 small mb-4">total saved</div>
                        <div class="small fw-semibold mt-auto" style="color: #fb7185;">plum / playful</div>
                    </div>
                </div>
            </div>

            {{-- Recent Orders Section --}}
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark">Recent Orders</h5>
                    <a href="{{ route('orders.index') }}" class="btn btn-outline-success btn-sm px-3">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table fr-table mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Business</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reservations as $res)
                                <tr>
                                    <td class="fw-semibold">#{{ $res->id }}</td>
                                    <td>{{ $res->business->business_name ?? 'N/A' }}</td>
                                    <td>{{ $res->created_at->format('M d, Y') }}</td>
                                    <td>Rs. {{ number_format($res->total_amount, 2) }}</td>
                                    <td>
                                        <span class="status-badge {{ $res->status }}">
                                            {{ ucfirst($res->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('customer.orders.show', $res->id) }}" class="btn btn-sm btn-success px-3">Details</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="fa fa-receipt fa-2x mb-3 text-muted"></i>
                                        <p class="mb-0">No recent orders found. Explore surplus food deals now!</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
