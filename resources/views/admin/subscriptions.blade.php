@extends('layouts.app')

@section('title', 'Subscriptions Management')

@section('content')
<div class="d-flex">
    @include('admin.sidebar')

    <div class="main-content flex-grow-1">
        <div class="page-header">
            <h4 class="page-title">Manage Subscriptions</h4>
            <span class="badge bg-danger status-badge active">Platform Access</span>
        </div>

        <div class="content-area">
            {{-- Status Tabs --}}
            <div class="d-flex flex-wrap gap-2 mb-4">
                <a href="{{ route('admin.subscriptions', ['status' => 'all']) }}" class="btn btn-sm {{ $status == 'all' ? 'btn-danger' : 'btn-outline-secondary' }}">All Subscriptions</a>
                <a href="{{ route('admin.subscriptions', ['status' => 'active']) }}" class="btn btn-sm {{ $status == 'active' ? 'btn-danger' : 'btn-outline-secondary' }}">Active</a>
                <a href="{{ route('admin.subscriptions', ['status' => 'expired']) }}" class="btn btn-sm {{ $status == 'expired' ? 'btn-danger' : 'btn-outline-secondary' }}">Expired</a>
                <a href="{{ route('admin.subscriptions', ['status' => 'cancelled']) }}" class="btn btn-sm {{ $status == 'cancelled' ? 'btn-danger' : 'btn-outline-secondary' }}">Cancelled</a>
            </div>

            {{-- Subscriptions Table --}}
            <div class="card border-0 shadow-sm rounded-3 bg-white">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table fr-table mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th>Business Partner</th>
                                    <th>Tier Plan</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Upload Limit</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($subscriptions as $sub)
                                <tr>
                                    <td class="fw-bold text-dark">{{ $sub->business->business_name ?? 'N/A' }}</td>
                                    <td><span class="badge bg-primary">{{ ucfirst($sub->plan_type) }}</span></td>
                                    <td>{{ $sub->start_date->format('M d, Y') }}</td>
                                    <td>{{ $sub->end_date->format('M d, Y') }}</td>
                                    <td>{{ $sub->upload_limit === -1 ? 'Unlimited' : $sub->upload_limit }} uploads</td>
                                    <td>
                                        <span class="status-badge {{ $sub->status }}">
                                            {{ ucfirst($sub->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            @if($sub->status === 'active')
                                                <form action="{{ route('admin.subscriptions.extend', $sub->id) }}" method="POST" class="d-flex gap-2 align-items-center">
                                                    @csrf
                                                    <input type="number" name="days" value="30" min="1" class="form-control form-control-sm" style="width: 70px;" required>
                                                    <button type="submit" class="btn btn-sm btn-outline-success">Extend</button>
                                                </form>
                                                <form action="{{ route('admin.subscriptions.cancel', $sub->id) }}" method="POST" onsubmit="return confirm('Cancel this subscription plan?')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">Cancel</button>
                                                </form>
                                            @else
                                                <span class="text-muted small">No actions</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="fa fa-credit-card fa-2x mb-3 text-muted"></i>
                                        <p class="mb-0">No subscriptions found matching this status.</p>
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
                {{ $subscriptions->appends(request()->input())->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection
