@extends('layouts.app')

@section('title', 'Manage Food Listings')

@section('content')
<div class="d-flex">
    @include('business.sidebar')

    <div class="main-content flex-grow-1">
        <div class="page-header">
            <h4 class="page-title">Manage Food Listings</h4>
            <a href="{{ route('business.food.create') }}" class="btn btn-success btn-sm px-3">
                <i class="fa fa-plus me-1"></i> Add Food Listing
            </a>
        </div>

        <div class="content-area">
            {{-- Listings stats --}}
            <div class="row g-3 mb-4 text-start">
                <div class="col-md-2.4 col-sm-6 flex-grow-1">
                    <div class="kpi-card p-3 bg-white">
                        <h4 class="fw-bold mb-0 text-dark">{{ $stats['total'] }}</h4>
                        <div class="small text-muted">Total Uploaded</div>
                    </div>
                </div>
                <div class="col-md-2.4 col-sm-6 flex-grow-1">
                    <div class="kpi-card p-3 bg-white">
                        <h4 class="fw-bold mb-0 text-success">{{ $stats['active'] }}</h4>
                        <div class="small text-muted">Active Deals</div>
                    </div>
                </div>
                <div class="col-md-2.4 col-sm-6 flex-grow-1">
                    <div class="kpi-card p-3 bg-white">
                        <h4 class="fw-bold mb-0 text-primary">{{ $stats['sold_out'] }}</h4>
                        <div class="small text-muted">Sold Out</div>
                    </div>
                </div>
                <div class="col-md-2.4 col-sm-6 flex-grow-1">
                    <div class="kpi-card p-3 bg-white">
                        <h4 class="fw-bold mb-0 text-danger">{{ $stats['expired'] }}</h4>
                        <div class="small text-muted">Expired Listings</div>
                    </div>
                </div>
                <div class="col-md-2.4 col-sm-6 flex-grow-1">
                    <div class="kpi-card p-3 bg-white border-danger">
                        <h4 class="fw-bold mb-0 text-danger">{{ $stats['high_risk'] }}</h4>
                        <div class="small text-muted">High Expiry Risk</div>
                    </div>
                </div>
            </div>

            {{-- Filter Bar --}}
            <div class="card border-0 shadow-sm rounded-3 bg-white mb-4 p-3 text-start">
                <form action="{{ route('business.food.index') }}" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Filter Category</label>
                        <select name="category" class="form-select">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Listing Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="sold_out" {{ request('status') === 'sold_out' ? 'selected' : '' }}>Sold Out</option>
                            <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">AI Expiry Risk</label>
                        <select name="risk" class="form-select">
                            <option value="">All Risks</option>
                            <option value="low" {{ request('risk') === 'low' ? 'selected' : '' }}>Low Risk</option>
                            <option value="medium" {{ request('risk') === 'medium' ? 'selected' : '' }}>Medium Risk</option>
                            <option value="high" {{ request('risk') === 'high' ? 'selected' : '' }}>High Risk</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-success w-100">Apply Filters</button>
                        <a href="{{ route('business.food.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                    </div>
                </form>
            </div>

            {{-- Inventory List --}}
            <div class="card border-0 shadow-sm rounded-3 bg-white">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table fr-table table-responsive-stack mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th>Food Item</th>
                                    <th>Category</th>
                                    <th>Price (Orig / Sale)</th>
                                    <th>Available Qty</th>
                                    <th>Expiry Time</th>
                                    <th>AI Expiry Risk</th>
                                    <th>Featured</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($foods as $food)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3 text-start">
                                            @if($food->image)
                                                <img src="{{ str_starts_with($food->image, 'assets/') ? asset($food->image) : asset('storage/' . $food->image) }}" alt="" class="rounded-3" style="width: 50px; height: 50px; object-fit: cover;">
                                            @else
                                                <div class="bg-light rounded-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; font-size: 1.5rem;">🍲</div>
                                            @endif
                                            <div>
                                                <div class="fw-bold text-dark">{{ $food->name }}</div>
                                                <div class="small text-muted">{{ Str::limit($food->description, 40) }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td data-label="Category">{{ $food->category->name }}</td>
                                    <td data-label="Price">
                                        <span class="text-decoration-line-through text-muted small">Rs. {{ number_format($food->original_price) }}</span>
                                        <div class="fw-bold text-success">Rs. {{ number_format($food->discount_price) }}</div>
                                    </td>
                                    <td class="fw-bold text-dark" data-label="Available Qty">{{ $food->available_quantity }} / {{ $food->quantity }}</td>
                                    <td data-label="Expiry Time">
                                        <div class="small fw-semibold text-dark">{{ $food->expiry_datetime->format('M d, H:i') }}</div>
                                        <div class="small {{ $food->expiry_datetime->isPast() ? 'text-danger' : 'text-muted' }}">{{ $food->expiry_datetime->diffForHumans() }}</div>
                                    </td>
                                    <td data-label="AI Expiry Risk">
                                        <span class="ai-risk-inline {{ $food->ai_risk_level ?: 'low' }}">
                                            AI: {{ ucfirst($food->ai_risk_level ?: 'low') }} Risk
                                        </span>
                                    </td>
                                    <td data-label="Featured">
                                        <form action="{{ route('business.food.featured', $food->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm {{ $food->is_featured ? 'btn-warning text-dark' : 'btn-outline-secondary' }}">
                                                {{ $food->is_featured ? '★ Featured' : '☆ Promote' }}
                                            </button>
                                        </form>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('business.food.edit', $food->id) }}" class="btn btn-sm btn-outline-success">Edit</a>
                                            <form action="{{ route('business.food.delete', $food->id) }}" method="POST" onsubmit="return confirm('Delete this food listing?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="fa fa-utensils fa-2x mb-3 text-muted"></i>
                                        <p class="mb-0">No food listings uploaded yet. List your surplus food now!</p>
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
                {{ $foods->appends(request()->input())->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection
