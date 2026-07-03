@extends('layouts.app')

@section('title', 'Commissions Ledger')

@section('content')
<div class="d-flex">
    @include('admin.sidebar')

    <div class="main-content flex-grow-1">
        <div class="page-header">
            <h4 class="page-title">Commissions Ledger</h4>
            <span class="badge bg-danger status-badge active">System Audit</span>
        </div>

        <div class="content-area">
            {{-- Summary stats --}}
            <div class="row g-4 mb-5">
                <div class="col-md-6">
                    <div class="kpi-card bg-white p-4 rounded-3 shadow-sm h-100">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div>
                                <span class="text-muted small text-uppercase fw-semibold">Total Pending Payout</span>
                                <h2 class="fw-extrabold text-warning mb-0 mt-1">Rs. {{ number_format($pendingTotal) }}</h2>
                            </div>
                            <div class="fs-2">⏳</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="kpi-card bg-white p-4 rounded-3 shadow-sm h-100">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div>
                                <span class="text-muted small text-uppercase fw-semibold">Total Settled Payout</span>
                                <h2 class="fw-extrabold text-success mb-0 mt-1">Rs. {{ number_format($settledTotal) }}</h2>
                            </div>
                            <div class="fs-2">✅</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tab Filters --}}
            <div class="d-flex flex-wrap gap-2 mb-4">
                <a href="{{ route('admin.commissions', ['status' => 'all']) }}" class="btn btn-sm {{ $status == 'all' ? 'btn-danger' : 'btn-outline-secondary' }}">All Logs</a>
                <a href="{{ route('admin.commissions', ['status' => 'pending']) }}" class="btn btn-sm {{ $status == 'pending' ? 'btn-danger' : 'btn-outline-secondary' }}">Pending</a>
                <a href="{{ route('admin.commissions', ['status' => 'settled']) }}" class="btn btn-sm {{ $status == 'settled' ? 'btn-danger' : 'btn-outline-secondary' }}">Settled</a>
            </div>

            {{-- Commissions Ledger Table --}}
            <div class="card border-0 shadow-sm rounded-3 bg-white">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table fr-table mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th>Business Partner</th>
                                    <th>Reservation Code</th>
                                    <th>Order Total</th>
                                    <th>Platform Fee</th>
                                    <th>Partner Share</th>
                                    <th>Status</th>
                                    <th>Settle Payout</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($commissions as $comm)
                                <tr>
                                    <td class="fw-bold text-dark">{{ $comm->business->business_name ?? 'N/A' }}</td>
                                    <td><code>{{ $comm->reservation->reservation_code ?? 'N/A' }}</code></td>
                                    <td>Rs. {{ number_format($comm->order_amount) }}</td>
                                    <td class="text-danger fw-semibold">Rs. {{ number_format($comm->commission_amount) }}</td>
                                    <td class="text-success fw-bold">Rs. {{ number_format($comm->business_earnings) }}</td>
                                    <td>
                                        <span class="status-badge {{ $comm->status }}">
                                            {{ ucfirst($comm->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($comm->status === 'pending')
                                            <form action="{{ route('admin.commissions.settle') }}" method="POST" onsubmit="return confirm('Settle all pending commissions for this business?')">
                                                @csrf
                                                <input type="hidden" name="business_id" value="{{ $comm->business_id }}">
                                                <button type="submit" class="btn btn-sm btn-success px-3">Settle</button>
                                            </form>
                                        @else
                                            <span class="text-muted small">Settled at {{ $comm->settled_at ? $comm->settled_at->format('M d, H:i') : 'N/A' }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="fa fa-percentage fa-2x mb-3 text-muted"></i>
                                        <p class="mb-0">No commissions logs found matching this status.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Pagination --}}
            <div class="mt-4">
                {{ $commissions->appends(request()->input())->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection
