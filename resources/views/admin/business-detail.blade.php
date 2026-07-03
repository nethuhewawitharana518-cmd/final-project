@extends('layouts.app')

@section('title', 'Business Details Review')

@section('content')
<div class="d-flex">
    @include('admin.sidebar')

    <div class="main-content flex-grow-1">
        <div class="page-header">
            <h4 class="page-title">Review Application: {{ $business->business_name }}</h4>
            <a href="{{ route('admin.businesses') }}" class="btn btn-outline-danger btn-sm px-3">Back to List</a>
        </div>

        <div class="content-area">
            <div class="row g-4">
                {{-- Merchant details card --}}
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3 p-4 bg-white mb-4">
                        <h5 class="fw-bold mb-4 text-dark">Business Profile</h5>
                        <hr>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="fw-bold small text-muted d-block">Business Name</label>
                                <span class="fs-6 text-dark fw-semibold">{{ $business->business_name }}</span>
                            </div>
                            <div class="col-md-6">
                                <label class="fw-bold small text-muted d-block">Type</label>
                                <span class="badge bg-success-subtle text-success mt-1">{{ ucfirst($business->business_type) }}</span>
                            </div>
                            <div class="col-md-6">
                                <label class="fw-bold small text-muted d-block">Owner Name</label>
                                <span class="text-dark">{{ $business->user->name ?? 'N/A' }}</span>
                            </div>
                            <div class="col-md-6">
                                <label class="fw-bold small text-muted d-block">Registration Number</label>
                                <span class="text-dark"><code>{{ $business->reg_number }}</code></span>
                            </div>
                            <div class="col-md-6">
                                <label class="fw-bold small text-muted d-block">Contact Email</label>
                                <span class="text-dark">{{ $business->email }}</span>
                            </div>
                            <div class="col-md-6">
                                <label class="fw-bold small text-muted d-block">Contact Phone</label>
                                <span class="text-dark">{{ $business->phone }}</span>
                            </div>
                            <div class="col-12">
                                <label class="fw-bold small text-muted d-block">Address</label>
                                <span class="text-dark">{{ $business->address }}</span>
                            </div>
                        </div>

                        @if($business->status === 'pending')
                            <div class="d-flex gap-3 mt-4 pt-3 border-top">
                                <form action="{{ route('admin.businesses.approve', $business->id) }}" method="POST" class="flex-grow-1" onsubmit="return confirm('Approve this business owner application?')">
                                    @csrf
                                    <button type="submit" class="btn btn-success w-100 py-2">Approve Merchant</button>
                                </form>
                                <button type="button" class="btn btn-danger flex-grow-1 py-2" data-bs-toggle="modal" data-bs-target="#rejectDetailModal">
                                    Reject Application
                                </button>
                            </div>
                        @else
                            <div class="mt-4 pt-3 border-top">
                                <label class="fw-bold small text-muted d-block mb-1">Current Verification Status</label>
                                <span class="status-badge {{ $business->status }} px-3 py-2 fs-6">{{ ucfirst($business->status) }}</span>
                                @if($business->status === 'rejected')
                                    <div class="alert alert-warning mt-3 mb-0 small">
                                        <strong>Rejection Reason:</strong> {{ $business->rejection_reason }}
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Submitted Documents review --}}
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3 p-4 bg-white mb-4">
                        <h5 class="fw-bold mb-4 text-dark">Verification Documents</h5>
                        <hr>
                        
                        <div class="mb-4">
                            <h6 class="fw-semibold text-dark mb-2"><i class="fa fa-file-pdf me-2 text-danger"></i>Registration Certificate</h6>
                            @if(isset($business->documents['reg_cert']))
                                <a href="{{ asset('storage/' . $business->documents['reg_cert']) }}" target="_blank" class="btn btn-sm btn-outline-success">View Certificate document</a>
                            @else
                                <span class="text-muted small">No document uploaded</span>
                            @endif
                        </div>

                        <div class="mb-4">
                            <h6 class="fw-semibold text-dark mb-2"><i class="fa fa-file-shield me-2 text-primary"></i>Food Safety Permit</h6>
                            @if(isset($business->documents['safety_permit']))
                                <a href="{{ asset('storage/' . $business->documents['safety_permit']) }}" target="_blank" class="btn btn-sm btn-outline-success">View safety permit document</a>
                            @else
                                <span class="text-muted small">No document uploaded</span>
                            @endif
                        </div>

                        @if($business->logo)
                            <div class="mb-4">
                                <h6 class="fw-semibold text-dark mb-2"><i class="fa fa-image me-2 text-success"></i>Business Logo</h6>
                                <img src="{{ asset('storage/' . $business->logo) }}" alt="Logo" class="img-thumbnail" style="max-width: 150px;">
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($business->status === 'pending')
    {{-- Rejection Detail Modal --}}
    <div class="modal fade" id="rejectDetailModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('admin.businesses.reject', $business->id) }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Reject Application: {{ $business->business_name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="reason_detail" class="form-label">Reason for Rejection</label>
                            <textarea name="reason" id="reason_detail" rows="3" class="form-control" placeholder="Provide a reason (min 5 characters)" required></textarea>
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
@endsection
