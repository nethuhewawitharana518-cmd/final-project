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
                <div class="col-md-3 col-sm-6">
                    <div class="kpi-card">
                        <div class="kpi-icon green">
                            <i class="fa fa-shopping-bag"></i>
                        </div>
                        <div>
                            <div class="kpi-value">{{ $activeOrdersCount }}</div>
                            <div class="kpi-label">Active Orders</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="kpi-card">
                        <div class="kpi-icon amber">
                            <i class="fa fa-hand-holding-usd"></i>
                        </div>
                        <div>
                            <div class="kpi-value">Rs. {{ number_format($savedAmount) }}</div>
                            <div class="kpi-label">Total Saved</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="kpi-card">
                        <div class="kpi-icon blue">
                            <i class="fa fa-star"></i>
                        </div>
                        <div>
                            <div class="kpi-value">{{ $loyaltyPoints }}</div>
                            <div class="kpi-label">Loyalty Points</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="kpi-card">
                        <div class="kpi-icon red">
                            <i class="fa fa-leaf"></i>
                        </div>
                        <div>
                            <div class="kpi-value">{{ number_format($co2Saved, 1) }} kg</div>
                            <div class="kpi-label">CO₂ Offset</div>
                        </div>
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
