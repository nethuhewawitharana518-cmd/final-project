@extends('layouts.app')

@section('title', 'Select Account Type')
@section('meta_description', 'Choose your FoodRescue account type — Sign up as a customer to buy deals or register your business to list food surplus.')

@section('content')
<section class="py-5 bg-light-gradient min-vh-80 d-flex align-items-center">
    <div class="container">
        <div class="row justify-content-center text-center mb-5">
            <div class="col-md-8">
                <span class="badge bg-success-subtle text-success text-uppercase fw-bold px-3 py-2 mb-3">Join us today</span>
                <h2 class="fw-bold text-dark mb-2">Create Your Account</h2>
                <p class="text-muted">Choose the option that matches how you would like to use FoodRescue.</p>
            </div>
        </div>

        <div class="row g-4 justify-content-center">
            {{-- Customer option card --}}
            <div class="col-md-5">
                <div class="card h-100 border-0 shadow-lg p-5 text-center bg-white rounded-3 hover-translate-y transition-all">
                    <div class="fs-1 mb-3">😋</div>
                    <h3 class="fw-bold text-dark mb-3">I want to Buy Food</h3>
                    <p class="text-muted small mb-4">
                        Discover deals from local hotels, bakeries, cafes, and supermarkets in Trincomalee. Save money, collect points, and prevent food waste.
                    </p>
                    <a href="{{ route('register.customer') }}" class="btn btn-success w-100 rounded-pill fw-semibold py-3 shadow-sm mt-auto">
                        Register as Customer
                    </a>
                </div>
            </div>

            {{-- Business option card --}}
            <div class="col-md-5">
                <div class="card h-100 border-0 shadow-lg p-5 text-center bg-white rounded-3 hover-translate-y transition-all">
                    <div class="fs-1 mb-3">💼</div>
                    <h3 class="fw-bold text-dark mb-3">I am a Business Owner</h3>
                    <p class="text-muted small mb-4">
                        Partner with us to list surplus food, minimize daily food waste, recover cost, and reach eco-conscious customers across the district.
                    </p>
                    <a href="{{ route('register.business') }}" class="btn btn-outline-success w-100 rounded-pill fw-semibold py-3 shadow-sm mt-auto">
                        Register Your Business
                    </a>
                </div>
            </div>
        </div>

        <div class="row justify-content-center text-center mt-5">
            <div class="col-12">
                <span class="text-muted small">Already have an account? </span>
                <a href="{{ route('login') }}" class="text-success small fw-semibold text-decoration-none">Sign In Here</a>
            </div>
        </div>
    </div>
</section>
@endsection
