@extends('layouts.app')

@section('title', 'Commission Statements')

@section('content')
<div class="d-flex">
    @include('business.sidebar')

    <div class="main-content flex-grow-1">
        <div class="page-header">
            <h4 class="page-title">Detailed Commission Statements</h4>
            <a href="{{ route('business.earnings') }}" class="btn btn-outline-secondary btn-sm px-3">
                <i class="fa fa-arrow-left me-1"></i> Back to Earnings
            </a>
        </div>

        <div class="content-area text-start">
            {{-- Transactions Table --}}
            <div class="card border-0 shadow-sm rounded-3 bg-white">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0 text-dark">Commission History Log</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table fr-table mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th>Statement ID</th>
                                    <th>Order Reference</th>
                                    <th>Transaction Date</th>
                                    <th>Sale Amount</th>
                                    <th>Commission Rate</th>
                                    <th>Commission Deduction</th>
                                    <th>Net Payout</th>
                                    <th>Settlement Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($commissions as $comm)
                                <tr>
                                    <td>#STMT-{{ $comm->id }}</td>
                                    <td>
                                        <a href="{{ route('business.reservations.show', $comm->reservation_id) }}" class="fw-bold text-success text-decoration-none">
                                            #{{ $comm->reservation_id }}
                                        </a>
                                    </td>
                                    <td>{{ $comm->created_at->format('M d, Y H:i') }}</td>
                                    <td class="fw-bold text-dark">Rs. {{ number_format($comm->sale_amount) }}</td>
                                    <td>{{ number_format(($comm->commission_amount / max(1, $comm->sale_amount)) * 100, 1) }}%</td>
                                    <td class="text-danger">- Rs. {{ number_format($comm->commission_amount) }}</td>
                                    <td class="fw-bold text-success">Rs. {{ number_format($comm->business_earnings) }}</td>
                                    <td>
                                        <span class="status-badge {{ $comm->status }}">
                                            {{ ucfirst($comm->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">No commission statement logs found.</td>
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
