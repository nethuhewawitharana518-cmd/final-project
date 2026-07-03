@extends('layouts.app')

@section('title', $food->name)
@section('meta_description', 'Get ' . $food->discount_percentage . '% off ' . $food->name . ' at ' . ($food->business->business_name ?? 'partner') . ' in Trincomalee. Save money and prevent food waste.')

@section('content')
<section class="py-4 bg-light border-bottom">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-success text-decoration-none">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('food.browse') }}" class="text-success text-decoration-none">Browse Deals</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $food->name }}</li>
            </ol>
        </nav>
    </div>
</section>

<section class="py-5 bg-white">
    <div class="container py-3">
        <div class="row g-5">
            {{-- Image Column --}}
            <div class="col-lg-6">
                <div class="position-relative food-detail-img-wrapper rounded-3 overflow-hidden shadow-sm">
                    <span class="badge bg-{{ $food->ai_risk_badge_color }} position-absolute top-0 start-0 m-4 z-3 text-uppercase fw-bold shadow-sm p-2">
                        AI Expiry Risk: {{ $food->ai_risk_level }}
                    </span>

                    @if($food->image)
                        <img src="{{ str_starts_with($food->image, 'assets/') ? asset($food->image) : asset('storage/' . $food->image) }}" class="img-fluid w-100 food-detail-img" alt="{{ $food->name }}">
                    @else
                        <div class="food-detail-fallback-img d-flex align-items-center justify-content-center bg-light text-success" style="height: 400px;">
                            <i class="fa {{ $food->category->icon ?? 'fa-bowl-food' }} fa-6x"></i>
                        </div>
                    @endif

                    <div class="discount-badge-large position-absolute bottom-0 end-0 m-4 bg-danger text-white px-4 py-2 rounded-pill fw-bold shadow fs-5">
                        {{ $food->discount_percentage }}% OFF
                    </div>
                </div>
            </div>

            {{-- Info & Action Column --}}
            <div class="col-lg-6">
                <div class="ps-lg-3">
                    {{-- Category and Business name --}}
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="badge bg-light text-secondary text-uppercase font-monospace px-3 py-2">
                            {{ $food->category->name ?? 'Other' }}
                        </span>
                        @if($food->is_featured)
                            <span class="badge bg-warning text-dark px-3 py-2 fw-bold">⭐ Featured</span>
                        @endif
                    </div>

                    {{-- Title --}}
                    <h1 class="display-6 fw-bold text-dark mb-2">{{ $food->name }}</h1>
                    
                    {{-- Business Provider --}}
                    <h5 class="text-muted mb-4">
                        Provided by: <a href="{{ route('food.browse', ['business' => $food->business_id]) }}" class="text-success text-decoration-none fw-semibold"><i class="fa fa-store me-1"></i>{{ $food->business->business_name ?? 'Partner Business' }}</a>
                    </h5>

                    <hr class="my-4">

                    {{-- Price and Status --}}
                    <div class="row align-items-center g-3 mb-4">
                        <div class="col-sm-6">
                            <span class="text-muted text-decoration-line-through small d-block">Original Price: Rs. {{ number_format($food->original_price, 2) }}</span>
                            <span class="text-success fw-bold display-6">Rs. {{ number_format($food->discount_price, 2) }}</span>
                        </div>
                        <div class="col-sm-6 text-sm-end">
                            <div class="d-inline-block p-3 bg-light rounded text-dark font-monospace small">
                                <i class="fa-regular fa-clock text-danger animate-pulse me-1"></i>
                                @if($food->hours_remaining <= 1)
                                    <strong class="text-danger">⏱ Expiring soon ({{ round($food->hours_remaining * 60) }}m)</strong>
                                @else
                                    <span>⏱ {{ number_format($food->hours_remaining, 1) }} hours remaining</span>
                                @endif
                                <div class="text-muted small mt-1">Expiry: {{ $food->expiry_datetime->format('d M H:i') }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Expiry Risk Alert / Dynamic Discount Reasoning --}}
                    <div class="alert alert-success-subtle border-0 p-4 mb-4 rounded-3 d-flex gap-3 align-items-start">
                        <div class="fs-3">💡</div>
                        <div>
                            <h6 class="fw-bold text-dark mb-1">AI Recommendation Insight</h6>
                            <p class="text-muted small mb-0">
                                This price includes a recommended discount generated dynamically based on item freshness, quantity remaining, and restaurant category parameters. SaveRs. {{ number_format($food->savings_amount, 2) }} and help prevent landfill food waste!
                            </p>
                        </div>
                    </div>

                    {{-- Add to Cart Card --}}
                    <div class="card border-0 shadow-sm p-4 bg-light rounded-3 mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-dark fw-bold">Stock Availability:</span>
                            @if($food->available_quantity > 0)
                                <span class="badge bg-success rounded-pill px-3">{{ $food->available_quantity }} items left</span>
                            @else
                                <span class="badge bg-danger rounded-pill px-3">Sold Out</span>
                            @endif
                        </div>

                        @if($food->available_quantity > 0)
                            @guest
                                <div class="text-center py-2">
                                    <p class="text-muted small mb-3">Please login as a customer to reserve this deal.</p>
                                    <a href="{{ route('login') }}" class="btn btn-success w-100 rounded-pill fw-semibold py-3 shadow-sm">
                                        <i class="fa fa-sign-in-alt me-2"></i>Login / Register
                                    </a>
                                </div>
                            @else
                                @if(auth()->user()->isCustomer())
                                    <form action="{{ route('customer.cart.add') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="food_id" value="{{ $food->id }}">
                                        
                                        <div class="row align-items-center g-3 mb-3">
                                            <div class="col-auto">
                                                <label for="quantity" class="text-dark small fw-semibold">Quantity to Order:</label>
                                            </div>
                                            <div class="col-auto">
                                                <input type="number" name="quantity" id="quantity" class="form-control text-center shadow-sm" style="width: 90px;" 
                                                    value="1" min="1" max="{{ $food->available_quantity }}" required>
                                            </div>
                                        </div>

                                        <button type="submit" class="btn btn-success w-100 rounded-pill fw-semibold py-3 shadow-sm mt-2">
                                            <i class="fa fa-shopping-cart me-2"></i>Reserve & Add to Cart
                                        </button>
                                    </form>
                                @else
                                    <div class="alert alert-warning py-2 text-center small mb-0">
                                        Only customers are authorized to book food items.
                                    </div>
                                @endif
                            @endguest
                        @else
                            <button class="btn btn-secondary w-100 rounded-pill fw-semibold py-3" disabled>Sold Out</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Business Description & Map Column --}}
        <div class="row g-5 mt-5 pt-3 border-top">
            <div class="col-lg-6">
                <h4 class="fw-bold text-dark mb-4">About the Vendor</h4>
                <div class="card border-0 shadow-sm p-4 bg-light h-100 rounded-3">
                    <h5 class="fw-semibold text-success mb-2">{{ $food->business->business_name }}</h5>
                    <p class="text-muted small mb-3"><i class="fa fa-map-marker-alt text-success me-2"></i>{{ $food->business->address }}</p>
                    <p class="text-muted small mb-4">
                        {{ $food->business->description ?: 'This business is a proud rescue partner committed to reducing food waste and serving the Trincomalee community.' }}
                    </p>
                    <div class="d-flex align-items-center gap-3">
                        <span class="badge bg-success-subtle text-success py-2 px-3 rounded text-capitalize"><i class="fa fa-store me-1"></i>{{ $food->business->business_type }}</span>
                        <span class="text-muted small"><i class="fa fa-phone me-1"></i>{{ $food->business->phone }}</span>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <h4 class="fw-bold text-dark mb-4">Pickup Location Map</h4>
                <div class="card border-0 shadow-sm overflow-hidden rounded-3" style="min-height: 280px;">
                    @if($food->business->latitude && $food->business->longitude)
                        <div id="foodDetailMap" style="height: 280px; width: 100%; border-radius: 12px;"></div>
                        <div class="p-3 border-top d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div>
                                <p class="mb-0 text-muted small"><i class="fa fa-map-marker-alt text-success me-1"></i>{{ $food->business->address }}</p>
                            </div>
                            <a href="https://www.google.com/maps/dir/?api=1&destination={{ urlencode($food->business->business_name . ', ' . $food->business->address) }}"
                               target="_blank" class="btn btn-success btn-sm px-4 rounded-pill">
                                <i class="fa fa-diamond-turn-right me-1"></i>Get Directions
                            </a>
                        </div>
                    @else
                        <div class="w-100 d-flex flex-column align-items-center justify-content-center bg-light text-muted p-4" style="min-height:280px;">
                            <i class="fa fa-map-location fa-3x mb-3 text-muted"></i>
                            <p class="small mb-0">No map coordinates configured for this business.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>


        {{-- Related Items --}}
        <div class="row mt-5 pt-3 border-top">
            <div class="col-12">
                <h4 class="fw-bold text-dark mb-4">More Deals from {{ $food->business->business_name }}</h4>
            </div>
            <div class="row g-4">
                @foreach($relatedFoods as $related)
                    <div class="col-sm-6 col-lg-3">
                        @include('partials.food-card', ['food' => $related])
                    </div>
                @endforeach

                @if($relatedFoods->isEmpty())
                    <div class="col-12 text-center py-5">
                        <span class="text-muted small">No other active deals from this business.</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection

@if($food->business->latitude && $food->business->longitude)
@push('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=geometry,places&callback=initFoodMap" async defer></script>
<script>
function initFoodMap() {
    const lat  = {{ (float)($food->business->latitude ?? 8.5842) }};
    const lng  = {{ (float)($food->business->longitude ?? 81.2312) }};
    const name = @json($food->business->business_name);
    const addr = @json($food->business->address);
    const type = @json($food->business->business_type);

    const TYPE_EMOJI = { hotel:'🏨', restaurant:'🍽️', bakery:'🥐', cafe:'☕', supermarket:'🛒' };
    const emoji = TYPE_EMOJI[type] || '📍';

    const pos = { lat: lat, lng: lng };
    const map = new google.maps.Map(document.getElementById('foodDetailMap'), {
        center: pos,
        zoom: 16,
        styles: [
            {
                "featureType": "poi.business",
                "elementType": "labels",
                "stylers": [
                    { "visibility": "off" }
                ]
            }
        ]
    });

    const marker = new google.maps.Marker({
        position: pos,
        map: map,
        title: name,
        label: emoji
    });

    const infoWindow = new google.maps.InfoWindow({
        content: `
            <div style="min-width:180px; text-align:center; padding:4px 0; font-family:sans-serif; color:#000;">
                <div style="font-size:1.6rem; margin-bottom:4px;">${emoji}</div>
                <strong style="color:#059669; font-size:.9rem;">${name}</strong><br>
                <span style="font-size:.75rem; color:#6b7280;">${addr}</span>
            </div>
        `
    });

    infoWindow.open(map, marker);
}
</script>
@endpush
@endif
