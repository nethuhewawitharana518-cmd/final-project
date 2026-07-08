@extends('layouts.app')

@section('title', 'Payment Ledger — Admin')

@section('content')
<div class="container-fluid py-4">

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="fa fa-receipt text-success me-2"></i>Payment Ledger</h4>
            <p class="text-muted small mb-0">All Stripe card payment transactions across the platform.</p>
        </div>
        <a href="{{ route('admin.payments.export') }}" class="btn btn-outline-success btn-sm px-3 rounded-pill">
            <i class="fa fa-file-csv me-1"></i>Export CSV
        </a>
    </div>

    {{-- Stats Row --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 text-center">
                <div class="text-muted small mb-1">Total Revenue</div>
                <div class="fw-bold fs-5 text-success">Rs. {{ number_format($stats['total_revenue'], 2) }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 text-center">
                <div class="text-muted small mb-1">Successful</div>
                <div class="fw-bold fs-5 text-primary">{{ $stats['total_count'] }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 text-center">
                <div class="text-muted small mb-1">Failed</div>
                <div class="fw-bold fs-5 text-danger">{{ $stats['failed_count'] }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 text-center">
                <div class="text-muted small mb-1">Refunded</div>
                <div class="fw-bold fs-5 text-warning">Rs. {{ number_format($stats['refunded_amount'], 2) }}</div>
            </div>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="row mb-4">
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 p-3 h-100">
                <h6 class="fw-bold mb-3 text-muted text-center">Payment Status Breakdown</h6>
                <div style="position: relative; height: 250px;">
                    <canvas id="paymentStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card border-0 shadow-sm rounded-3 p-3 mb-4">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Search (Transaction ID / Code)</label>
                <input type="text" name="search" class="form-control form-control-sm"
                       value="{{ request('search') }}" placeholder="pi_xxx or FR-...">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All</option>
                    <option value="success"  {{ request('status')=='success'  ? 'selected':'' }}>Success</option>
                    <option value="failed"   {{ request('status')=='failed'   ? 'selected':'' }}>Failed</option>
                    <option value="pending"  {{ request('status')=='pending'  ? 'selected':'' }}>Pending</option>
                    <option value="refunded" {{ request('status')=='refunded' ? 'selected':'' }}>Refunded</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">From Date</label>
                <input type="date" name="from" class="form-control form-control-sm" value="{{ request('from') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">To Date</label>
                <input type="date" name="to" class="form-control form-control-sm" value="{{ request('to') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-success btn-sm w-100 rounded-pill">
                    <i class="fa fa-filter me-1"></i>Filter
                </button>
            </div>
            <div class="col-md-1">
                <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary btn-sm w-100 rounded-pill">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Payments Table --}}
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="table-responsive">
            <table class="table fr-table table-hover align-middle mb-0 small">
                <thead>
                    <tr>
                        <th class="fw-semibold py-3 ps-3">Transaction</th>
                        <th class="fw-semibold">Customer</th>
                        <th class="fw-semibold">Business</th>
                        <th class="fw-semibold">Card</th>
                        <th class="fw-semibold text-end">Amount</th>
                        <th class="fw-semibold text-center">Status</th>
                        <th class="fw-semibold">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                    <tr>
                        <td class="ps-3 py-3">
                            <div class="fw-semibold text-dark" style="font-size:.78rem;">
                                {{ $payment->reservation?->reservation_code ?? '—' }}
                            </div>
                            <code class="text-muted" style="font-size:.68rem;word-break:break-all;">
                                {{ Str::limit($payment->transaction_id ?? 'N/A', 28) }}
                            </code>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $payment->user?->name ?? 'N/A' }}</div>
                            <div class="text-muted" style="font-size:.72rem;">{{ $payment->user?->email }}</div>
                        </td>
                        <td class="text-dark">{{ $payment->reservation?->business?->business_name ?? '—' }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <i class="{{ $payment->card_brand_icon }} fs-5"></i>
                                <div>
                                    <div class="fw-semibold text-dark">{{ ucfirst($payment->card_brand ?? $payment->gateway) }}</div>
                                    @if($payment->card_last4)
                                        <div class="text-muted" style="font-size:.72rem;">···· {{ $payment->card_last4 }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="text-end fw-bold text-dark">
                            Rs. {{ number_format($payment->amount, 2) }}
                            <div class="text-muted fw-normal" style="font-size:.72rem;">{{ strtoupper($payment->currency ?? 'lkr') }}</div>
                        </td>
                        <td class="text-center">
                            @php
                            $badge = match($payment->status) {
                                'success'  => 'success',
                                'failed'   => 'danger',
                                'pending'  => 'warning',
                                'refunded' => 'secondary',
                                default    => 'light',
                            };
                            @endphp
                            <span class="badge bg-{{ $badge }} rounded-pill px-3">{{ ucfirst($payment->status) }}</span>
                        </td>
                        <td class="text-muted" style="font-size:.78rem;">
                            {{ $payment->paid_at?->format('M d, Y') ?? $payment->created_at->format('M d, Y') }}
                            <div>{{ $payment->paid_at?->format('H:i') ?? $payment->created_at->format('H:i') }}</div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fa fa-receipt fa-3x mb-3 d-block opacity-25"></i>
                            No payment records found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
        <div class="p-3 border-top">
            {{ $payments->links() }}
        </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('paymentStatusChart');
        if (ctx) {
            new Chart(ctx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Successful', 'Pending', 'Failed'],
                    datasets: [{
                        data: [
                            {{ $stats['total_count'] ?? 0 }},
                            {{ $stats['pending_count'] ?? 0 }},
                            {{ $stats['failed_count'] ?? 0 }}
                        ],
                        backgroundColor: [
                            '#198754', // success
                            '#ffc107', // pending
                            '#dc3545'  // failed
                        ],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: '#a0aec0',
                                padding: 20,
                                font: { size: 12, family: "'Plus Jakarta Sans', sans-serif" }
                            }
                        }
                    },
                    cutout: '70%'
                }
            });
        }
    });
</script>
@endpush
