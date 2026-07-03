@extends('layouts.app')

@section('title', 'Business Dashboard')

@section('content')
<div class="d-flex">
    @include('business.sidebar')

    <div class="main-content flex-grow-1">
        <div class="page-header">
            <h4 class="page-title">Merchant Panel: {{ $business->business_name }}</h4>
            <span class="status-badge active">Approved Business</span>
        </div>

        <div class="content-area">
            {{-- KPI Summary Cards --}}
            <div class="row g-4 mb-5">
                <div class="col-md-3 col-sm-6">
                    <div class="kpi-card bg-white h-100">
                        <div class="kpi-icon green">
                            <i class="fa fa-hand-holding-usd"></i>
                        </div>
                        <div>
                            <div class="kpi-value">Rs. {{ number_format($todayEarnings) }}</div>
                            <div class="kpi-label">Today's Earnings</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="kpi-card bg-white h-100">
                        <div class="kpi-icon blue">
                            <i class="fa fa-utensils"></i>
                        </div>
                        <div>
                            <div class="kpi-value">{{ $activeListingsCount }}</div>
                            <div class="kpi-label">Active Listings</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="kpi-card bg-white h-100">
                        <div class="kpi-icon amber">
                            <i class="fa fa-spinner"></i>
                        </div>
                        <div>
                            <div class="kpi-value">{{ $pendingReservationsCount }}</div>
                            <div class="kpi-label">Pending Bookings</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="kpi-card bg-white h-100">
                        <div class="kpi-icon red">
                            <i class="fa fa-percentage"></i>
                        </div>
                        <div>
                            <div class="kpi-value">Rs. {{ number_format($totalCommissionPaid) }}</div>
                            <div class="kpi-label">Commission Paid</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Analytics Summary Widgets --}}
            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <div class="kpi-card bg-white h-100">
                        <div class="kpi-icon green">
                            <i class="fa fa-chart-line"></i>
                        </div>
                        <div>
                            <div class="kpi-value">Rs. {{ number_format($totalRevenueThisMonth, 2) }}</div>
                            <div class="kpi-label">Total Revenue (This Month)</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="kpi-card bg-white h-100">
                        <div class="kpi-icon blue">
                            <i class="fa fa-calculator"></i>
                        </div>
                        <div>
                            <div class="kpi-value">Rs. {{ number_format($averageOrderValue, 2) }}</div>
                            <div class="kpi-label">Average Order Value</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="kpi-card bg-white h-100">
                        <div class="kpi-icon amber">
                            <i class="fa fa-clock-rotate-left"></i>
                        </div>
                        <div>
                            <div class="kpi-value">Rs. {{ number_format($pendingPayouts, 2) }}</div>
                            <div class="kpi-label">Pending Payouts</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Visual Charts Section --}}
            <div class="row g-4 mb-5">
                {{-- Monthly Sales Income --}}
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3 bg-white p-4">
                        <h5 class="fw-bold text-white mb-4"><i class="fa fa-chart-bar text-success me-2"></i>Monthly Income Trends ({{ now()->year }})</h5>
                        <div id="monthlyIncomeChart" style="min-height: 350px;"></div>
                    </div>
                </div>

                {{-- Daily Earnings Overview --}}
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3 bg-white p-4">
                        <h5 class="fw-bold text-white mb-4"><i class="fa fa-chart-area text-success me-2"></i>Daily Earnings (Last 15 Days)</h5>
                        <div id="dailyEarningsChart" style="min-height: 350px;"></div>
                    </div>
                </div>
            </div>

            {{-- AI Insights / Expiry Risk Alerts --}}
            @if($aiInsights->isNotEmpty())
            <div class="alert alert-warning border-0 shadow-sm rounded-3 p-4 mb-5 text-start">
                <h5 class="fw-bold mb-3"><i class="fa fa-brain me-2 text-danger"></i>AI Food Expiry Risk Alerts</h5>
                <p class="small text-muted mb-4">The following food listings are at high/medium risk of expiring unsold. Click 'Optimize' to modify pricing or details based on AI recommendations.</p>
                <div class="row g-3">
                    @foreach($aiInsights as $item)
                    <div class="col-12 p-3 bg-white rounded-3 border d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <span class="fw-bold text-dark">{{ $item->name }}</span>
                            <span class="ai-risk-inline {{ strtolower($item->ai_risk_level) }} ms-2">
                                {{ ucfirst($item->ai_risk_level) }} Expiry Risk
                            </span>
                            <div class="small text-muted mt-1">Expires: {{ $item->expiry_datetime->format('M d, H:i') }} ({{ $item->expiry_datetime->diffForHumans() }})</div>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="text-end small">
                                <div class="text-muted">AI Rec Discount:</div>
                                <span class="fw-bold" style="color: var(--primary);">{{ $item->ai_recommended_discount }}% OFF</span>
                            </div>
                            <a href="{{ route('business.food.edit', $item->id) }}" class="btn btn-sm btn-outline-success">Optimize Pricing</a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Recent Bookings Table --}}
            <div class="card border-0 shadow-sm rounded-3 bg-white">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark">Recent Customer Bookings</h5>
                    <a href="{{ route('business.reservations') }}" class="btn btn-outline-success btn-sm px-3">All Bookings</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table fr-table mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th>Reservation Code</th>
                                    <th>Customer</th>
                                    <th>Reserved Items</th>
                                    <th>Total Price</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentReservations as $res)
                                <tr>
                                    <td class="fw-semibold">{{ $res->reservation_code }}</td>
                                    <td>{{ $res->customer->name ?? 'Guest User' }}</td>
                                    <td>
                                        @foreach($res->items as $item)
                                            <span class="badge bg-light text-dark">{{ $item->food->name ?? 'Food' }} (x{{ $item->quantity }})</span>
                                        @endforeach
                                    </td>
                                    <td class="fw-bold" style="color: var(--primary);">Rs. {{ number_format($res->total_amount, 2) }}</td>
                                    <td>
                                        <span class="status-badge {{ $res->status }}">
                                            {{ ucfirst($res->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('business.reservations.show', $res->id) }}" class="btn btn-sm btn-success px-3">View</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="fa fa-receipt fa-2x mb-3 text-muted"></i>
                                        <p class="mb-0">No customer bookings logged yet.</p>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Premium Dark Mode color variables
    const primaryColor = '#059669'; // Success emerald
    const secondaryColor = '#3b82f6'; // Blue
    const textColor = '#9ca3af'; // Gray muted text
    const gridColor = '#374151'; // Dark border/grid color

    // Monthly Income Chart (Bar Chart)
    var monthlyOptions = {
        series: [{
            name: 'Earnings (Rs.)',
            data: @json($monthlyEarnings)
        }],
        chart: {
            type: 'bar',
            height: 350,
            toolbar: { show: false },
            foreColor: textColor,
            background: 'transparent'
        },
        plotOptions: {
            bar: {
                borderRadius: 6,
                columnWidth: '50%',
                distributed: false,
            }
        },
        colors: [primaryColor],
        dataLabels: { enabled: false },
        stroke: { show: true, width: 2, colors: ['transparent'] },
        xaxis: {
            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            labels: {
                formatter: function (val) {
                    return "Rs. " + val.toLocaleString();
                }
            }
        },
        grid: {
            borderColor: gridColor,
            strokeDashArray: 4
        },
        fill: {
            type: 'gradient',
            gradient: {
                shade: 'dark',
                type: "vertical",
                shadeIntensity: 0.5,
                gradientToColors: ['#34d399'], // lighter emerald
                inverseColors: true,
                opacityFrom: 0.85,
                opacityTo: 0.95,
                stops: [0, 100]
            }
        },
        tooltip: {
            theme: 'dark',
            y: {
                formatter: function (val) {
                    return "Rs. " + val.toLocaleString();
                }
            }
        }
    };

    var monthlyChart = new ApexCharts(document.querySelector("#monthlyIncomeChart"), monthlyOptions);
    monthlyChart.render();

    // Daily Earnings Overview Chart (Area/Line Chart)
    var dailyOptions = {
        series: [{
            name: 'Earnings (Rs.)',
            data: @json($dailyEarnings)
        }],
        chart: {
            type: 'area',
            height: 350,
            toolbar: { show: false },
            foreColor: textColor,
            background: 'transparent'
        },
        colors: [secondaryColor],
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 3 },
        xaxis: {
            categories: @json($dailyLabels),
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            labels: {
                formatter: function (val) {
                    return "Rs. " + val.toLocaleString();
                }
            }
        },
        grid: {
            borderColor: gridColor,
            strokeDashArray: 4
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.45,
                opacityTo: 0.05,
                stops: [0, 100]
            }
        },
        tooltip: {
            theme: 'dark',
            y: {
                formatter: function (val) {
                    return "Rs. " + val.toLocaleString();
                }
            }
        }
    };

    var dailyChart = new ApexCharts(document.querySelector("#dailyEarningsChart"), dailyOptions);
    dailyChart.render();
});
</script>
@endpush
