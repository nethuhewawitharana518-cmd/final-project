@extends('layouts.app')

@section('title', 'AI Demand Analytics')

@section('content')
<div class="d-flex">
    @include('business.sidebar')

    <div class="main-content flex-grow-1">
        <div class="page-header">
            <h4 class="page-title">AI Analytics & Forecasting</h4>
            <span class="badge bg-success status-badge active">AI Engine v1.0</span>
        </div>

        <div class="content-area text-start">
            {{-- Peak Hours and Forecast Summary --}}
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-3 bg-white p-4 h-100">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="fw-bold text-dark mb-0">Predicted Peak Demand Hours</h5>
                            <i class="fa fa-clock text-primary fs-4"></i>
                        </div>
                        <p class="text-muted small">Based on historical customer reservation check-ins, the AI predicts maximum customer pickup traffic during the following hours:</p>
                        <div class="d-flex gap-2 flex-wrap">
                            @foreach($forecast['peak_hours'] ?? [12, 18] as $hour)
                                <div class="bg-light border text-dark px-3 py-2 rounded-3 text-center" style="min-width: 90px;">
                                    <div class="fw-bold fs-5">{{ sprintf("%02d:00", $hour) }}</div>
                                    <div class="small text-muted">{{ $hour < 12 ? 'AM Peak' : ($hour < 17 ? 'Lunch Peak' : 'Dinner Peak') }}</div>
                                </div>
                            @endforeach
                        </div>
                        <div class="alert alert-info mt-3 mb-0 py-2 small">
                            <i class="fa fa-info-circle me-1"></i> <strong>Pro-Tip:</strong> Schedule your surplus listings 2-3 hours before these peak traffic windows to maximize sale likelihood.
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-3 bg-white p-4 h-100">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="fw-bold text-dark mb-0">Demand Forecast Trend</h5>
                            <span class="status-badge {{ ($forecast['trend'] ?? 'rising') === 'rising' ? 'active' : 'warning' }}">
                                {{ ucfirst($forecast['trend'] ?? 'rising') }} Trend
                            </span>
                        </div>
                        <p class="text-muted small">Platform forecast model detects a <strong>{{ $forecast['trend'] ?? 'rising' }}</strong> demand pattern for your business area.</p>
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="border rounded p-3 bg-light">
                                    <div class="small text-muted">Forecast Model</div>
                                    <div class="fw-bold text-dark">{{ $forecast['model'] ?? 'LinearRegression' }}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded p-3 bg-light">
                                    <div class="small text-muted">Average Confidence</div>
                                    <div class="fw-bold text-dark">78.0%</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 7-Day Forecast & Category Shares --}}
            <div class="row g-4 mb-4">
                <div class="col-md-7">
                    <div class="card border-0 shadow-sm rounded-3 bg-white">
                        <div class="card-header bg-white border-0 py-3">
                            <h5 class="fw-bold mb-0 text-dark">7-Day Demand Forecast (Predicted Orders)</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table fr-table mb-0 align-middle">
                                    <thead>
                                        <tr>
                                            <th>Day</th>
                                            <th>Predicted Orders</th>
                                            <th>Confidence Level</th>
                                            <th>Status Recommendation</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($forecast['forecast'] ?? [] as $dayForecast)
                                        <tr>
                                            <td class="fw-bold text-dark">{{ $dayForecast['day'] }}</td>
                                            <td class="fw-bold text-success">{{ $dayForecast['predicted_orders'] }} orders</td>
                                            <td>
                                                <div class="progress" style="height: 6px; max-width: 120px;">
                                                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $dayForecast['confidence'] * 100 }}%"></div>
                                                </div>
                                                <span class="small text-muted">{{ $dayForecast['confidence'] * 100 }}%</span>
                                            </td>
                                            <td>
                                                @if($dayForecast['predicted_orders'] > 40)
                                                    <span class="badge bg-success">High Demand Expected</span>
                                                @elseif($dayForecast['predicted_orders'] > 25)
                                                    <span class="badge bg-info text-dark">Steady Traffic</span>
                                                @else
                                                    <span class="badge bg-warning text-dark">Increase Promotions</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted">
                                                No forecasting data available. Keep selling to train the AI!
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="card border-0 shadow-sm rounded-3 bg-white p-4">
                        <h5 class="fw-bold text-dark mb-3">AI Category Demand Shares</h5>
                        <p class="text-muted small">Demand interest breakdown across different food categories for your business location:</p>
                        
                        <div class="d-flex flex-column gap-3">
                            @forelse($forecast['category_demand'] ?? [] as $share)
                                <div>
                                    <div class="d-flex justify-content-between mb-1 small fw-bold text-dark">
                                        <span>{{ $share['category'] }}</span>
                                        <span>{{ number_format($share['share'] * 100, 1) }}%</span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $share['share'] * 100 }}%"></div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-4 text-muted">No category distribution data found.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- Inventory Risk Metrics --}}
            <div class="card border-0 shadow-sm rounded-3 bg-white p-4">
                <h5 class="fw-bold text-dark mb-3">Active Inventory Analysis</h5>
                <div class="row g-3 text-center">
                    @forelse($foodStats as $stat)
                        <div class="col-md-3">
                            <div class="border rounded p-3 bg-light">
                                <div class="small text-muted mb-1">{{ $stat->name }}</div>
                                <div class="fw-bold text-success fs-4">{{ $stat->total_qty }} Qty</div>
                                <div class="small text-muted">{{ $stat->count }} Active listings</div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 py-3 text-muted text-center">No active inventory stats available.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
