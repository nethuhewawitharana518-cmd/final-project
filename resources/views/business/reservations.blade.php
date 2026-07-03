@extends('layouts.app')

@section('title', 'Manage Reservations')

@section('content')
<div class="d-flex">
    @include('business.sidebar')

    <div class="main-content flex-grow-1">
        <div class="page-header">
            <h4 class="page-title">Customer Bookings</h4>
            <span class="badge bg-success status-badge active">Reservations Ledger</span>
        </div>

        <div class="content-area text-start">
            {{-- Status Filters --}}
            <div class="d-flex flex-wrap gap-2 mb-4">
                <a href="{{ route('business.reservations', ['status' => 'all']) }}" class="btn btn-sm {{ $status == 'all' ? 'btn-success' : 'btn-outline-secondary' }}">All</a>
                <a href="{{ route('business.reservations', ['status' => 'pending']) }}" class="btn btn-sm {{ $status == 'pending' ? 'btn-success' : 'btn-outline-secondary' }}">Pending Scan</a>
                <a href="{{ route('business.reservations', ['status' => 'confirmed']) }}" class="btn btn-sm {{ $status == 'confirmed' ? 'btn-success' : 'btn-outline-secondary' }}">Confirmed</a>
                <a href="{{ route('business.reservations', ['status' => 'paid']) }}" class="btn btn-sm {{ $status == 'paid' ? 'btn-success' : 'btn-outline-secondary' }}">Paid</a>
                <a href="{{ route('business.reservations', ['status' => 'collected']) }}" class="btn btn-sm {{ $status == 'collected' ? 'btn-success' : 'btn-outline-secondary' }}">Collected</a>
                <a href="{{ route('business.reservations', ['status' => 'cancelled']) }}" class="btn btn-sm {{ $status == 'cancelled' ? 'btn-success' : 'btn-outline-secondary' }}">Cancelled</a>
            </div>

            {{-- Table --}}
            <div class="card border-0 shadow-sm bg-white rounded-3">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table fr-table mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th>Order Code</th>
                                    <th>Customer</th>
                                    <th>Quantity</th>
                                    <th>Price Total</th>
                                    <th>Reservation Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reservations as $res)
                                <tr>
                                    <td class="fw-semibold">{{ $res->reservation_code }}</td>
                                    <td>{{ $res->customer->name ?? 'Guest User' }}</td>
                                    <td>{{ $res->items->sum('quantity') }} items</td>
                                    <td class="fw-bold text-success">Rs. {{ number_format($res->total_amount, 2) }}</td>
                                    <td>{{ $res->created_at->format('M d, Y H:i') }}</td>
                                    <td>
                                        <span class="status-badge {{ $res->status }}">
                                            {{ ucfirst($res->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('business.reservations.show', $res->id) }}" class="btn btn-sm btn-success px-3">View Detail</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">No reservations log matched.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                {{ $reservations->appends(request()->input())->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection
