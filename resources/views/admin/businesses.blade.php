@extends('layouts.app')

@section('title', 'Business Approvals')

@section('content')
<div class="d-flex">
    @include('admin.sidebar')

    <div class="main-content flex-grow-1">
        <div class="page-header">
            <h4 class="page-title">Business Approvals</h4>
            <span class="badge bg-danger status-badge active">Verification Hub</span>
        </div>

        <div class="content-area">
            {{-- Status Tabs --}}
            <div class="d-flex flex-wrap gap-2 mb-4">
                <a href="{{ route('admin.businesses', ['status' => 'pending']) }}" class="btn btn-sm {{ $status == 'pending' ? 'btn-danger' : 'btn-outline-secondary' }}">Pending Review</a>
                <a href="{{ route('admin.businesses', ['status' => 'approved']) }}" class="btn btn-sm {{ $status == 'approved' ? 'btn-success' : 'btn-outline-secondary' }}">Approved (Active)</a>
                <a href="{{ route('admin.businesses', ['status' => 'rejected']) }}" class="btn btn-sm {{ $status == 'rejected' ? 'btn-warning' : 'btn-outline-secondary' }}">Rejected</a>
            </div>

            {{-- Partners List Table --}}
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table fr-table mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th>Business Name</th>
                                    <th>Type</th>
                                    <th>Owner</th>
                                    <th>Email/Phone</th>
                                    <th>Reg Number</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($businesses as $biz)
                                <tr>
                                    <td class="fw-bold text-dark">{{ $biz->business_name }}</td>
                                    <td><span class="badge bg-secondary-subtle text-secondary">{{ ucfirst($biz->business_type) }}</span></td>
                                    <td>{{ $biz->user->name ?? 'N/A' }}</td>
                                    <td>
                                        <div class="small text-muted">{{ $biz->email }}</div>
                                        <div class="small text-muted">{{ $biz->phone }}</div>
                                    </td>
                                    <td><code>{{ $biz->reg_number }}</code></td>
                                    <td>
                                        <span class="status-badge {{ $biz->status }}">
                                            {{ ucfirst($biz->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('admin.businesses.show', $biz->id) }}" class="btn btn-sm btn-outline-success">Review Documents</a>
                                            @if($biz->status === 'pending')
                                                <form action="{{ route('admin.businesses.approve', $biz->id) }}" method="POST" onsubmit="return confirm('Approve this business?')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                                </form>
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $biz->id }}">
                                                    Reject
                                                </button>

                                                {{-- Reject Modal --}}
                                                <div class="modal fade" id="rejectModal{{ $biz->id }}" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <form action="{{ route('admin.businesses.reject', $biz->id) }}" method="POST">
                                                            @csrf
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Reject Business: {{ $biz->business_name }}</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body text-start">
                                                                    <div class="mb-3">
                                                                        <label for="reason" class="form-label">Reason for Rejection</label>
                                                                        <textarea name="reason" id="reason" rows="3" class="form-control" placeholder="Provide a reason (min 5 characters)" required></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                    <button type="submit" class="btn btn-danger">Confirm Rejection</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="fa fa-folder-open fa-2x mb-3 text-muted"></i>
                                        <p class="mb-0">No business applications found matching this status.</p>
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
                {{ $businesses->appends(request()->input())->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection
