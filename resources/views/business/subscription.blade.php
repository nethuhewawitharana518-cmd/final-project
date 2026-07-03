@extends('layouts.app')

@section('title', 'Business Subscription')

@section('content')
<div class="d-flex">
    @include('business.sidebar')

    <div class="main-content flex-grow-1">
        <div class="page-header">
            <h4 class="page-title">Manage Subscriptions</h4>
            <span class="badge bg-success status-badge active">Plan Settings</span>
        </div>

        <div class="content-area text-start">
            {{-- Active subscription details --}}
            <div class="card border-0 shadow-sm bg-white p-4 rounded-3 mb-5">
                <h5 class="fw-bold mb-3 text-dark">Your Active Subscription</h5>
                <hr>
                @if($activeSubscription)
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="small text-muted fw-bold d-block">Tier Plan</label>
                            <span class="badge bg-success fs-6 mt-1">{{ ucfirst($activeSubscription->plan_type) }}</span>
                        </div>
                        <div class="col-md-4">
                            <label class="small text-muted fw-bold d-block">Monthly Price</label>
                            <span class="text-dark fw-bold fs-6">Rs. {{ number_format($activeSubscription->price) }}</span>
                        </div>
                        <div class="col-md-4">
                            <label class="small text-muted fw-bold d-block">Upload Limit</label>
                            <span class="text-dark fw-bold fs-6">
                                {{ $activeSubscription->upload_limit === -1 ? 'Unlimited' : $activeSubscription->upload_limit }} uploads/mo
                            </span>
                        </div>
                        <div class="col-md-4">
                            <label class="small text-muted fw-bold d-block">Start Date</label>
                            <span class="text-dark">{{ $activeSubscription->start_date->format('M d, Y') }}</span>
                        </div>
                        <div class="col-md-4">
                            <label class="small text-muted fw-bold d-block">Renewal/End Date</label>
                            <span class="text-dark fw-semibold">{{ $activeSubscription->end_date->format('M d, Y') }}</span>
                        </div>
                        <div class="col-md-4">
                            <label class="small text-muted fw-bold d-block">Status</label>
                            <span class="status-badge active mt-1">Active & Valid</span>
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning mb-0">
                        <i class="fa fa-triangle-exclamation me-2"></i>You do not have any active subscription plan. Please select a plan below to start listing food.
                    </div>
                @endif
            </div>

            {{-- Plans Selection --}}
            <h5 class="fw-bold text-dark mb-4">Choose Your Subscription Plan</h5>
            <div class="row g-4 mb-5">
                @foreach($plans as $type => $plan)
                <div class="col-md-4">
                    <div class="plan-card h-100 p-4 {{ ($activeSubscription && $activeSubscription->plan_type === $type) ? 'popular' : '' }}">
                        <h4 class="fw-bold text-dark mb-1">{{ $plan['name'] }}</h4>
                        <div class="plan-price-main mb-3">Rs. {{ number_format($plan['price']) }}<span class="fs-6 text-muted">/mo</span></div>
                        <ul class="plan-feature-list text-start">
                            @if($type === 'starter')
                                <li>50 food uploads / month</li>
                                <li>AI expiry risk prediction indicators</li>
                                <li>Real-time dashboard analytics</li>
                                <li>QR verified collections system</li>
                            @elseif($type === 'professional')
                                <li>Max 250 food uploads / month</li>
                                <li>AI expiry risk prediction indicators</li>
                                <li>Real-time dashboard analytics</li>
                                <li>QR verified collections system</li>
                            @elseif($type === 'enterprise')
                                <li>Unlimited food uploads / month</li>
                                <li>Advanced AI Analytics & custom reports</li>
                                <li>24/7 Priority merchant support</li>
                                <li>Featured placement on the customer homepage banner</li>
                            @endif
                        </ul>
                        <a href="{{ route('business.payment', ['plan' => $type]) }}" class="btn btn-success w-100 py-2 mt-4 rounded-pill">
                            {{ ($activeSubscription && $activeSubscription->plan_type === $type) ? 'Renew Plan' : 'Select Plan' }}
                        </a>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- History Section --}}
            <div class="card border-0 shadow-sm rounded-3 bg-white">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0 text-dark">Billing History</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table fr-table mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th>Plan Name</th>
                                    <th>Price</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($history as $item)
                                <tr>
                                    <td class="fw-bold text-dark">{{ ucfirst($item->plan_type) }}</td>
                                    <td>Rs. {{ number_format($item->price) }}</td>
                                    <td>{{ $item->start_date->format('M d, Y') }}</td>
                                    <td>{{ $item->end_date->format('M d, Y') }}</td>
                                    <td>
                                        <span class="status-badge {{ $item->status }}">
                                            {{ ucfirst($item->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No billing history logs found.</td>
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
