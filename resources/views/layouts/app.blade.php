<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('meta_description', 'AI-Powered Food Rescue Marketplace — Save food, save money in Trincomalee District.')">
    <title>@yield('title', 'FoodRescue') | Food Rescue Marketplace Trincomalee</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{{ asset('assets/css/custom.css') }}?v={{ time() }}" rel="stylesheet">
    @stack('styles')
</head>
<body>

<!-- ─── Navbar ─────────────────────────────────────────────── -->
<nav class="navbar navbar-expand-lg navbar-light fr-navbar sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
            <div class="brand-icon">🥗</div>
            <span class="brand-text">FoodRescue</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav me-auto gap-1">
                <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('food.browse') }}">Browse Food</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('map') }}"><i class="fa fa-map-location-dot me-1"></i>Live Map</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('about') }}">About</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('contact') }}">Contact</a></li>
            </ul>

            <ul class="navbar-nav gap-2 align-items-center">
                @guest
                    <li class="nav-item">
                        <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm px-3">Login</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('register') }}" class="btn btn-success btn-sm px-3">Get Started</a>
                    </li>
                @else
                    <!-- Notifications Bell -->
                    <li class="nav-item dropdown">
                        <a class="nav-link position-relative" href="#" data-bs-toggle="dropdown" id="notificationBellLink">
                            <i class="fa fa-bell"></i>
                            <span id="notificationBellBadge" class="badge bg-danger position-absolute top-0 start-100 translate-middle badge-sm {{ auth()->user()->getUnreadNotificationsCount() > 0 ? '' : 'd-none' }}">
                                {{ auth()->user()->getUnreadNotificationsCount() }}
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end notification-dropdown shadow">
                            <li class="dropdown-header fw-semibold">Notifications</li>
                            @foreach(auth()->user()->notifications()->latest()->limit(5)->get() as $notif)
                                <li>
                                    <a class="dropdown-item {{ $notif->is_read ? '' : 'fw-semibold' }}" href="{{ $notif->action_url ?: '#' }}">
                                        <small class="text-muted d-block mb-1">{{ $notif->created_at->diffForHumans() }}</small>
                                        <span class="d-block {{ $notif->is_read ? 'text-white-50' : 'text-white' }}" style="font-size: 0.85rem;">{{ Str::limit($notif->message, 60) }}</span>
                                    </a>
                                </li>
                            @endforeach
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                @if(auth()->user()->isCustomer())
                                    <a class="dropdown-item text-center text-success" href="{{ route('customer.notifications') }}">View All</a>
                                @endif
                            </li>
                        </ul>
                    </li>

                    <!-- Cart (customer only) -->
                    @if(auth()->user()->isCustomer())
                    <li class="nav-item">
                        <a href="{{ route('customer.cart') }}" class="nav-link position-relative">
                            <i class="fa fa-shopping-cart"></i>
                            @if(session('cart') && count(session('cart')) > 0)
                                <span class="badge bg-danger position-absolute top-0 start-100 translate-middle badge-sm">
                                    {{ count(session('cart')) }}
                                </span>
                            @endif
                        </a>
                    </li>
                    @endif

                    <!-- User menu -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" data-bs-toggle="dropdown">
                            @if(auth()->user()->avatar)
                                <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}" class="user-avatar-sm" style="object-fit: cover;">
                            @else
                                <div class="user-avatar-sm">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                            @endif
                            <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                            @if(auth()->user()->isCustomer())
                                <li><a class="dropdown-item" href="{{ route('customer.dashboard') }}"><i class="fa fa-dashboard me-2 text-success"></i>Dashboard</a></li>
                                <li><a class="dropdown-item" href="{{ route('customer.orders') }}"><i class="fa fa-box me-2 text-primary"></i>My Orders</a></li>
                                <li><a class="dropdown-item" href="{{ route('customer.loyalty') }}"><i class="fa fa-star me-2 text-warning"></i>Loyalty Points</a></li>
                                <li><a class="dropdown-item" href="{{ route('customer.profile') }}"><i class="fa fa-user me-2"></i>Profile</a></li>
                            @elseif(auth()->user()->isBusinessOwner())
                                <li><a class="dropdown-item" href="{{ route('business.dashboard') }}"><i class="fa fa-store me-2 text-success"></i>Business Panel</a></li>
                                <li><a class="dropdown-item" href="{{ route('business.food.index') }}"><i class="fa fa-utensils me-2"></i>Manage Food</a></li>
                                <li><a class="dropdown-item" href="{{ route('business.profile') }}"><i class="fa fa-gears me-2 text-warning"></i>Business Profile</a></li>
                            @elseif(auth()->user()->isAdmin())
                                <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}"><i class="fa fa-shield-alt me-2 text-danger"></i>Admin Panel</a></li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="fa fa-sign-out me-2"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>

<!-- ─── Flash Messages ────────────────────────────────────── -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show m-0 rounded-0 global-top-alert" role="alert">
        <div class="container"><i class="fa fa-check-circle me-2"></i>{{ session('success') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show m-0 rounded-0 global-top-alert" role="alert">
        <div class="container"><i class="fa fa-exclamation-circle me-2"></i>{{ session('error') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show m-0 rounded-0 global-top-alert" role="alert">
        <div class="container"><i class="fa fa-triangle-exclamation me-2"></i>{{ session('warning') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- ─── Page Content ──────────────────────────────────────── -->
@yield('content')

<!-- ─── Footer ───────────────────────────────────────────── -->
<footer class="fr-footer mt-auto">
    <div class="container py-5">
        <div class="row g-4">
            <div class="col-lg-4">
                <h5 class="brand-text mb-3">🥗 FoodRescue</h5>
                <p class="text-white-50 small">Connecting surplus food businesses with savvy customers in Trincomalee District. Save food. Save money. Save the planet.</p>
                <div class="d-flex gap-3 mt-3">
                    <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>
            <div class="col-lg-2 col-6">
                <h6 class="fw-semibold mb-3 text-white">Quick Links</h6>
                <ul class="list-unstyled small">
                    <li><a href="{{ route('home') }}" class="footer-link">Home</a></li>
                    <li><a href="{{ route('food.browse') }}" class="footer-link">Browse Food</a></li>
                    <li><a href="{{ route('about') }}" class="footer-link">About Us</a></li>
                    <li><a href="{{ route('contact') }}" class="footer-link">Contact</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-6">
                <h6 class="fw-semibold mb-3 text-white">For Businesses</h6>
                <ul class="list-unstyled small">
                    <li><a href="{{ route('register.business') }}" class="footer-link">Register Business</a></li>
                    <li><a href="{{ route('login') }}" class="footer-link">Business Login</a></li>
                    <li><a href="#" class="footer-link">Subscription Plans</a></li>
                </ul>
            </div>
            <div class="col-lg-4">
                <h6 class="fw-semibold mb-3 text-white">Contact</h6>
                <ul class="list-unstyled small text-white-50">
                    <li><i class="fa fa-map-marker-alt me-2 text-primary"></i>Trincomalee, Eastern Province, Sri Lanka</li>
                    <li class="mt-2"><i class="fa fa-envelope me-2 text-primary"></i><a href="mailto:info.foodrescue@gmail.com" class="text-white-50 text-decoration-none">info.foodrescue@gmail.com</a></li>
                    <li class="mt-2"><i class="fab fa-whatsapp me-2 text-primary"></i><a href="https://wa.me/94716787083" class="text-white-50 text-decoration-none" target="_blank">+94 71 678 7083</a></li>
                </ul>
            </div>
        </div>
        <hr class="mt-4 border-secondary">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <p class="text-white-50 small mb-0">&copy; {{ date('Y') }} FoodRescue Marketplace. All rights reserved.</p>
            <p class="text-white-50 small mb-0">🌱 Supporting UN SDG 12.3 — Zero Food Waste</p>
        </div>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<!-- Custom JS -->
<script src="{{ asset('assets/js/app.js') }}"></script>
@auth
    @if(auth()->user()->isCustomer())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const badge = document.getElementById('notificationBellBadge');
            
            function checkUnreadCount() {
                fetch('{{ route("customer.notifications.unread-count") }}', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.unread_count > 0) {
                        badge.innerText = data.unread_count;
                        badge.classList.remove('d-none');
                    } else {
                        badge.classList.add('d-none');
                    }
                })
                .catch(error => console.error('Error fetching notifications:', error));
            }

            // Check every 10 seconds for demo responsiveness
            setInterval(checkUnreadCount, 10000);
        });
    </script>
    @endif
@endauth
@stack('scripts')
</body>
</html>
