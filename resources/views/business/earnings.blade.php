@extends('layouts.app')

@section('title', 'Earnings Ledger')

@section('content')
<div class="d-flex">
    @include('business.sidebar')

    <div class="main-content flex-grow-1">
        <div class="page-header">
            <h4 class="page-title">Earnings Summary</h4>
            <a href="{{ route('business.earnings.export') }}" class="btn btn-success btn-sm px-3">
                <i class="fa fa-download me-1"></i> Export Report
            </a>
        </div>

        <div class="content-area text-start">
            {{-- KPI Summary Cards --}}
            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <div class="kpi-card bg-white h-100">
                        <div class="kpi-icon green">
                            <i class="fa fa-shopping-bag text-success"></i>
                        </div>
                        <div>
                            <div class="kpi-value">Rs. {{ number_format($totalSales) }}</div>
                            <div class="kpi-label">Gross Sales</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="kpi-card bg-white h-100">
                        <div class="kpi-icon red">
                            <i class="fa fa-percentage text-danger"></i>
                        </div>
                        <div>
                            <div class="kpi-value">Rs. {{ number_format($totalCommission) }}</div>
                            <div class="kpi-label">Platform Commission Cut</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="kpi-card bg-white h-100">
                        <div class="kpi-icon blue">
                            <i class="fa fa-wallet text-primary"></i>
                        </div>
                        <div>
                            <div class="kpi-value">Rs. {{ number_format($totalEarnings) }}</div>
                            <div class="kpi-label">Your Net Earnings</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Transactions Table --}}
            <div class="card border-0 shadow-sm rounded-3 bg-white">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0 text-dark">Payout Transactions</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table fr-table mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th>Order Reference</th>
                                    <th>Date</th>
                                    <th>Gross Amount</th>
                                    <th>Commission Fee</th>
                                    <th>Net Payout</th>
                                    <th>Payout Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($commissions as $comm)
                                <tr>
                                    <td>#{{ $comm->reservation_id }}</td>
                                    <td>{{ $comm->created_at->format('M d, Y H:i') }}</td>
                                    <td class="fw-bold text-dark">Rs. {{ number_format($comm->sale_amount) }}</td>
                                    <td class="text-danger">Rs. {{ number_format($comm->commission_amount) }}</td>
                                    <td class="fw-bold text-success">Rs. {{ number_format($comm->business_earnings) }}</td>
                                    <td>
                                        <span class="status-badge {{ $comm->status }}">
                                            {{ ucfirst($comm->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">No payout transaction records available.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                {{ $commissions->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection
