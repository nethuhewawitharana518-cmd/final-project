@extends('layouts.app')

@section('title', 'Browse Food Deals')
@section('meta_description', 'Browse and search surplus food deals from local restaurants, cafes, hotels and bakeries in Trincomalee. Save up to 80%.')

@section('content')
<section class="py-4 bg-light border-bottom">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-primary fw-semibold text-decoration-none">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Browse Deals</li>
            </ol>
        </nav>
    </div>
</section>

<section class="py-5 bg-white">
    <div class="container">
        <div class="row g-4">
            {{-- Filter Sidebar Column --}}
            <div class="col-lg-3">
                <div class="card border-0 shadow-sm p-4 bg-light rounded-3 sticky-top" style="top: 90px; z-index: 10;">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold text-dark mb-0"><i class="fa fa-filter me-2 text-primary"></i>Filters</h5>
                        <a href="{{ route('food.browse') }}" class="text-primary text-decoration-none small fw-semibold">Clear All</a>
                    </div>

                    <form action="{{ route('food.browse') }}" method="GET">
                        {{-- Search --}}
                        <div class="mb-4">
                            <label for="search" class="form-label text-dark small fw-semibold">Search Deals</label>
                            <div class="input-group shadow-sm">
                                <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa fa-search"></i></span>
                                <input type="text" name="q" id="search" class="form-control border-start-0 ps-0" placeholder="Biryani, cake..." value="{{ request('q') }}">
                            </div>
                        </div>

                        {{-- Category --}}
                        <div class="mb-4">
                            <label class="form-label text-dark small fw-semibold d-block">Categories</label>
                            <div class="d-flex flex-column gap-2 overflow-auto" style="max-height: 200px;">
                                @foreach($categories as $cat)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="category[]" value="{{ $cat->id }}" id="cat_{{ $cat->id }}" 
                                            @if(is_array(request('category')) && in_array($cat->id, request('category'))) checked @elseif(request('category') == $cat->slug) checked @endif>
                                        <label class="form-check-label text-muted small" for="cat_{{ $cat->id }}">
                                            {{ $cat->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Sort option --}}
                        <div class="mb-4">
                            <label for="sort" class="form-label text-dark small fw-semibold">Sort By</label>
                            <select name="sort" id="sort" class="form-select shadow-sm" onchange="this.form.submit()">
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest Listed</option>
                                <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                                <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                                <option value="expiring_soon" {{ request('sort') == 'expiring_soon' ? 'selected' : '' }}>Expiring Soonest</option>
                                <option value="discount_high" {{ request('sort') == 'discount_high' ? 'selected' : '' }}>Highest Discount</option>
                            </select>
                        </div>

                        {{-- Expiry Hours --}}
                        <div class="mb-4">
                            <label for="expiry_hours" class="form-label text-dark small fw-semibold">Time Remaining</label>
                            <select name="expiry_hours" id="expiry_hours" class="form-select shadow-sm">
                                <option value="">Any Time</option>
                                <option value="2" {{ request('expiry_hours') == '2' ? 'selected' : '' }}>Expiring in 2 hours</option>
                                <option value="6" {{ request('expiry_hours') == '6' ? 'selected' : '' }}>Expiring in 6 hours</option>
                                <option value="12" {{ request('expiry_hours') == '12' ? 'selected' : '' }}>Expiring in 12 hours</option>
                                <option value="24" {{ request('expiry_hours') == '24' ? 'selected' : '' }}>Expiring in 24 hours</option>
                            </select>
                        </div>

                        {{-- AI Risk --}}
                        <div class="mb-4">
                            <label for="ai_risk" class="form-label text-dark small fw-semibold">AI Expiry Risk</label>
                            <select name="ai_risk" id="ai_risk" class="form-select shadow-sm">
                                <option value="">All Risks</option>
                                <option value="high" {{ request('ai_risk') == 'high' ? 'selected' : '' }}>🚨 High Risk</option>
                                <option value="medium" {{ request('ai_risk') == 'medium' ? 'selected' : '' }}>⚠️ Medium Risk</option>
                                <option value="low" {{ request('ai_risk') == 'low' ? 'selected' : '' }}>✅ Low Risk</option>
                            </select>
                        </div>

                        {{-- Price filter --}}
                        <div class="mb-4">
                            <label for="max_price" class="form-label text-dark small fw-semibold">Max Price (Rs.)</label>
                            <input type="number" name="max_price" id="max_price" class="form-control shadow-sm" placeholder="e.g. 500" value="{{ request('max_price') }}">
                        </div>

                        <button type="submit" class="btn btn-success w-100 rounded-pill fw-semibold shadow-sm mt-2">
                            Apply Filters
                        </button>
                    </form>
                </div>
            </div>

            {{-- Grid Column --}}
            <div class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <h4 class="fw-bold text-dark mb-0">Active Deals <span class="badge bg-primary rounded-pill fs-6 fw-normal ms-2">{{ $foods->total() }} available</span></h4>
                    <span class="text-muted small">Showing {{ $foods->firstItem() ?? 0 }}–{{ $foods->lastItem() ?? 0 }} of {{ $foods->total() }} results</span>
                </div>

                <div class="row g-4">
                    @foreach($foods as $food)
                        <div class="col-md-6 col-xl-4">
                            @include('partials.food-card', ['food' => $food])
                        </div>
                    @endforeach

                    @if($foods->isEmpty())
                        <div class="col-12 text-center py-5 bg-light rounded-3 shadow-sm border border-dashed">
                            <i class="fa fa-utensils fa-3x text-primary mb-3 opacity-50"></i>
                            <h5 class="fw-bold text-dark">No Deals Found</h5>
                            <p class="text-muted small mb-0">Try clearing filters or search terms to see available offers.</p>
                        </div>
                    @endif
                </div>

                {{-- Pagination Links --}}
                <div class="d-flex justify-content-center mt-5">
                    {{ $foods->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
