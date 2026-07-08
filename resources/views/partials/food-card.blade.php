<div class="card h-100 food-card position-relative transition-all hover-translate-y">
    {{-- Expiry Risk Badge --}}
    <span class="ai-risk-inline {{ strtolower($food->ai_risk_level) }} position-absolute top-0 start-0 m-3 z-3 shadow-sm">
        AI Risk: {{ $food->ai_risk_level }}
    </span>

    @if($food->is_featured)
        <span class="badge-discount-hero position-absolute top-0 end-0 m-3 z-3 shadow-sm bg-warning text-dark">
            ⭐ Featured
        </span>
    @endif

    {{-- Food Image or Fallback Category Icon --}}
    <div class="position-relative food-card-img">
        @if($food->image)
            <img src="{{ str_starts_with($food->image, 'assets/') ? asset($food->image) : asset('storage/' . $food->image) }}" alt="{{ $food->name }}">
        @else
            <div class="d-flex align-items-center justify-content-center h-100 bg-light text-primary">
                <i class="fa {{ $food->category->icon ?? 'fa-bowl-food' }} fa-4x opacity-50"></i>
            </div>
        @endif
        
        {{-- Discount Percentage Badge overlay --}}
        <div class="badge-discount-hero position-absolute bottom-0 end-0 m-3 shadow">
            {{ $food->discount_percentage }}% OFF
        </div>
    </div>

    <div class="food-card-body">
        {{-- Category and Business name --}}
        <div class="d-flex justify-content-between align-items-center mb-2 gap-2 w-100">
            <span class="badge bg-light text-secondary text-uppercase font-monospace small px-2 py-1 border flex-shrink-0">
                {{ $food->category->name ?? 'Other' }}
            </span>
            <div class="d-flex align-items-center gap-1 text-end">
                @if($food->business && $food->business->badge)
                    <span class="badge rounded-circle shadow-sm px-2
                        {{ $food->business->badge === '1st' ? 'bg-warning text-dark' : ($food->business->badge === '2nd' ? 'bg-secondary text-white' : 'bg-danger text-white') }}" 
                        title="Top Rated: {{ $food->business->badge }} Place Vendor!" style="font-size: 0.75rem;">
                        @if($food->business->badge === '1st') 🥇
                        @elseif($food->business->badge === '2nd') 🥈
                        @elseif($food->business->badge === '3rd') 🥉
                        @endif
                    </span>
                @endif
                <span class="food-biz-name m-0 text-truncate" style="max-width: 120px; display: inline-block;" title="{{ $food->business->business_name ?? 'Partner' }}">
                    <i class="fa fa-store me-1 text-primary"></i>{{ $food->business->business_name ?? 'Partner' }}
                </span>
            </div>
        </div>

        @if($food->business && $food->business->review_count > 0)
            <div class="small text-warning mb-2" style="font-size: 0.8rem;">
                ⭐ <span class="fw-bold">{{ number_format($food->business->average_rating, 1) }}</span> 
                <span class="text-muted">({{ $food->business->review_count }} {{ Str::plural('review', $food->business->review_count) }})</span>
            </div>
        @endif

        {{-- Food Name --}}
        <h5 class="food-name limit-text-2">
            {{ $food->name }}
        </h5>

        {{-- Description --}}
        <p class="text-muted small mb-3 limit-text-3">
            {{ $food->description ?: 'No description provided.' }}
        </p>

        {{-- Expiry Countdown / Time remaining --}}
        <div class="d-flex align-items-center gap-2 mb-4 p-2 bg-light rounded text-dark font-monospace small mt-auto border">
            <i class="fa-regular fa-clock text-danger animate-pulse"></i>
            <span>
                @if($food->hours_remaining <= 1)
                    <strong class="text-danger">⏱ Expiring soon ({{ round($food->hours_remaining * 60) }}m)</strong>
                @else
                    <span>⏱ {{ number_format($food->hours_remaining, 1) }} hours left</span>
                @endif
            </span>
        </div>

        {{-- Pricing and Action button --}}
        <div class="d-flex align-items-center justify-content-between mt-auto pt-3 border-top">
            <div>
                <span class="price-original d-block">Rs. {{ number_format($food->original_price, 2) }}</span>
                <span class="price-discounted">Rs. {{ number_format($food->discount_price, 2) }}</span>
            </div>
            <a href="{{ route('food.detail', $food->id) }}" class="btn btn-success px-4 py-2 shadow-sm">
                View Deal
            </a>
        </div>
    </div>
</div>
