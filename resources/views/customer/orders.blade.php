@extends('layouts.app')

@section('title', 'My Orders')

@section('content')
<div class="d-flex">
    @include('customer.sidebar')

    <div class="main-content flex-grow-1">
        <div class="page-header">
            <h4 class="page-title">My Orders</h4>
            <span class="badge bg-success status-badge active">Order History</span>
        </div>

        <div class="content-area">
            {{-- Status Filters --}}
            <div class="d-flex flex-wrap gap-2 mb-4">
                <a href="{{ route('orders.index', ['status' => 'all']) }}" class="btn btn-sm {{ $status == 'all' ? 'btn-success' : 'btn-outline-secondary' }}">All</a>
                <a href="{{ route('orders.index', ['status' => 'pending']) }}" class="btn btn-sm {{ $status == 'pending' ? 'btn-success' : 'btn-outline-secondary' }}">Pending</a>
                <a href="{{ route('orders.index', ['status' => 'confirmed']) }}" class="btn btn-sm {{ $status == 'confirmed' ? 'btn-success' : 'btn-outline-secondary' }}">Confirmed</a>
                <a href="{{ route('orders.index', ['status' => 'paid']) }}" class="btn btn-sm {{ $status == 'paid' ? 'btn-success' : 'btn-outline-secondary' }}">Paid</a>
                <a href="{{ route('orders.index', ['status' => 'collected']) }}" class="btn btn-sm {{ $status == 'collected' ? 'btn-success' : 'btn-outline-secondary' }}">Collected</a>
                <a href="{{ route('orders.index', ['status' => 'cancelled']) }}" class="btn btn-sm {{ $status == 'cancelled' ? 'btn-success' : 'btn-outline-secondary' }}">Cancelled</a>
            </div>

            {{-- Orders List --}}
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table fr-table mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th>Order Code</th>
                                    <th>Business</th>
                                    <th>Items</th>
                                    <th>Total Price</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                <tr>
                                    <td class="fw-semibold">{{ $order->reservation_code }}</td>
                                    <td>{{ $order->business->business_name ?? 'N/A' }}</td>
                                    <td>
                                        @foreach($order->items as $item)
                                            <span class="badge bg-light text-dark">{{ $item->food->name ?? 'Item' }} (x{{ $item->quantity }})</span>
                                        @endforeach
                                    </td>
                                    <td class="fw-bold">Rs. {{ number_format($order->total_amount, 2) }}</td>
                                    <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                                    <td>
                                        <span class="status-badge {{ $order->status }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('customer.orders.show', $order->id) }}" class="btn btn-sm btn-success px-3">View</a>
                                            @if($order->status === 'pending')
                                                <form action="{{ route('customer.orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this order?')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">Cancel</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="fa fa-receipt fa-2x mb-3 text-muted"></i>
                                        <p class="mb-0">No orders found matching the filter.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Pagination Links --}}
            <div class="mt-4">
                {{ $orders->appends(request()->input())->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection
