@extends('layouts.app')

@section('title', 'Reports Overview')

@section('content')
<div class="d-flex">
    @include('admin.sidebar')

    <div class="main-content flex-grow-1">
        <div class="page-header">
            <h4 class="page-title">Sales & Activity Reports</h4>
            <span class="badge bg-danger status-badge active">Analytics Console</span>
        </div>

        <div class="content-area">
            {{-- Visual Text Metrics --}}
            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm bg-success text-white p-4 rounded-3 h-100">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="text-uppercase fw-semibold mb-0">Monthly Income</h6>
                            <i class="fa fa-money-bill-wave fs-4"></i>
                        </div>
                        <h2 class="fw-bold mb-1">Rs. {{ number_format($monthlyIncome) }}</h2>
                        <span class="small opacity-75">Accumulated sales in {{ date('F Y') }}</span>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-0 shadow-sm bg-primary text-white p-4 rounded-3 h-100">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="text-uppercase fw-semibold mb-0">Food Uploads</h6>
                            <i class="fa fa-utensils fs-4"></i>
                        </div>
                        <h2 class="fw-bold mb-1">{{ number_format($totalFoodUploads) }}</h2>
                        <span class="small opacity-75">Surplus food items uploaded</span>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-0 shadow-sm bg-warning text-white p-4 rounded-3 h-100">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="text-uppercase fw-semibold mb-0">Completed Orders</h6>
                            <i class="fa fa-box-open fs-4"></i>
                        </div>
                        <h2 class="fw-bold mb-1">{{ number_format($completedOrdersCount) }}</h2>
                        <span class="small opacity-75">Rescued foods collected by customers</span>
                    </div>
                </div>
            </div>

            {{-- Top Performing Hotels & Reports CSV Downloads --}}
            <div class="row g-4 mb-5">
                {{-- CSV Exports --}}
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm bg-white p-4 h-100 rounded-3">
                        <h5 class="fw-bold mb-4 text-dark">Data Exports (CSV)</h5>
                        <hr>
                        <div class="d-grid gap-3">
                            <div class="p-3 border rounded-3 d-flex align-items-center justify-content-between bg-light">
                                <div>
                                    <h6 class="fw-semibold text-dark mb-1">Food Waste Analytics</h6>
                                    <small class="text-muted">Total food saved and remaining surplus</small>
                                </div>
                                <a href="{{ route('admin.reports.export', 'food_waste') }}" class="btn btn-sm btn-success">
                                    <i class="fa fa-download me-1"></i> Export
                                </a>
                            </div>

                            <div class="p-3 border rounded-3 d-flex align-items-center justify-content-between bg-light">
                                <div>
                                    <h6 class="fw-semibold text-dark mb-1">Financial Ledger</h6>
                                    <small class="text-muted">Breakdown of system commissions & fees</small>
                                </div>
                                <a href="{{ route('admin.reports.export', 'revenue') }}" class="btn btn-sm btn-success">
                                    <i class="fa fa-download me-1"></i> Export
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Top Performing Businesses --}}
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm bg-white p-4 h-100 rounded-3">
                        <h5 class="fw-bold mb-4 text-dark">Top-Performing Hotels & Restaurants</h5>
                        <hr>
                        <div class="table-responsive">
                            <table class="table fr-table mb-0 align-middle">
                                <thead>
                                    <tr>
                                        <th>Business Name</th>
                                        <th>Type</th>
                                        <th>Contact Email</th>
                                        <th class="text-end">Completed Rescues</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($topBusinesses as $biz)
                                    <tr>
                                        <td class="fw-semibold text-dark">{{ $biz->business_name }}</td>
                                        <td><span class="badge bg-secondary-subtle text-secondary">{{ ucfirst($biz->business_type) }}</span></td>
                                        <td>{{ $biz->email }}</td>
                                        <td class="text-end fw-bold text-success">{{ $biz->reservations_count }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">No top-performing partners logged yet.</td>
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
</div>
@endsection
