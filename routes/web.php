<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\FoodController as PublicFoodController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboard;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\OrderController;
use App\Http\Controllers\Customer\LoyaltyController;
use App\Http\Controllers\Customer\ProfileController as CustomerProfile;
use App\Http\Controllers\Customer\NotificationController as CustomerNotification;
use App\Http\Controllers\Business\DashboardController as BusinessDashboard;
use App\Http\Controllers\Business\SubscriptionController;
use App\Http\Controllers\Business\FoodController as BusinessFoodController;
use App\Http\Controllers\Business\ReservationController as BusinessReservation;
use App\Http\Controllers\Business\QRScannerController;
use App\Http\Controllers\Business\EarningsController;
use App\Http\Controllers\Business\AnalyticsController;
use App\Http\Controllers\Business\ProfileController as BusinessProfile;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\BusinessApprovalController;
use App\Http\Controllers\Admin\SubscriptionManagementController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\RevenueController;
use App\Http\Controllers\Admin\CommissionController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingsController;

// ═══════════════════════════════════════════════════════════
//  PUBLIC ROUTES
// ═══════════════════════════════════════════════════════════
Route::get('/',           [HomeController::class, 'index'])->name('home');
Route::get('/about',      [HomeController::class, 'about'])->name('about');
Route::get('/contact',    [HomeController::class, 'contact'])->name('contact');
Route::post('/contact',   [HomeController::class, 'sendContact'])->name('contact.send');

Route::get('/browse',          [PublicFoodController::class, 'index'])->name('food.browse');
Route::get('/food/{id}',       [PublicFoodController::class, 'show'])->name('food.detail');
Route::get('/food/search',     [PublicFoodController::class, 'search'])->name('food.search');

// ═══════════════════════════════════════════════════════════
//  AUTHENTICATION ROUTES
// ═══════════════════════════════════════════════════════════
Route::middleware('guest')->group(function () {
    Route::get('/login',                    [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login',                   [LoginController::class, 'login'])->name('login.post');
    Route::get('/register',                 [RegisterController::class, 'selectType'])->name('register');
    Route::get('/register/customer',        [RegisterController::class, 'showCustomerForm'])->name('register.customer');
    Route::post('/register/customer',       [RegisterController::class, 'registerCustomer'])->name('register.customer.post');
    Route::get('/register/business',        [RegisterController::class, 'showBusinessForm'])->name('register.business');
    Route::post('/register/business',       [RegisterController::class, 'registerBusiness'])->name('register.business.post');
    Route::get('/forgot-password',          [ForgotPasswordController::class, 'show'])->name('password.request');
    Route::post('/forgot-password',         [ForgotPasswordController::class, 'sendLink'])->name('password.email');
    Route::get('/verify-otp',               [ForgotPasswordController::class, 'showOtpForm'])->name('password.otp.show');
    Route::post('/verify-otp',              [ForgotPasswordController::class, 'verifyOtp'])->name('password.otp.verify');
    Route::get('/reset-password-otp',       [ForgotPasswordController::class, 'showResetForm'])->name('password.reset.otp');
    Route::post('/reset-password-otp',      [ForgotPasswordController::class, 'resetPassword'])->name('password.update.otp');
});

Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

// Global dashboard router with role-based redirection
Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user->isAdmin()) {
        return redirect()->route('admin.dashboard');
    } elseif ($user->isBusinessOwner()) {
        return redirect()->route('business.dashboard');
    }
    return redirect()->route('customer.dashboard');
})->middleware('auth')->name('dashboard');

// Named routes requested for sidebar buttons
Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::get('/customer/dashboard-view', [CustomerDashboard::class, 'index'])->name('dashboard.view');
    Route::get('/customer/orders-view', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/customer/loyalty-view', [LoyaltyController::class, 'index'])->name('loyalty.index');
});

// ═══════════════════════════════════════════════════════════
//  CUSTOMER ROUTES
// ═══════════════════════════════════════════════════════════
Route::prefix('customer')->name('customer.')->middleware(['auth', 'role:customer'])->group(function () {
    Route::get('/dashboard',                [CustomerDashboard::class, 'index'])->name('dashboard');

    // Cart
    Route::get('/cart',                     [CartController::class, 'index'])->name('cart');
    Route::post('/cart/add',                [CartController::class, 'add'])->name('cart.add');
    Route::put('/cart/{id}',                [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{id}',             [CartController::class, 'remove'])->name('cart.remove');
    Route::delete('/cart',                  [CartController::class, 'clear'])->name('cart.clear');

    // Checkout — Stripe 2-Step Flow
    Route::get('/checkout',                 [CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout/delivery-options',[CheckoutController::class, 'calculateDelivery'])->name('checkout.delivery-options');
    Route::post('/checkout/intent',         [CheckoutController::class, 'createPaymentIntent'])->name('checkout.intent');
    Route::post('/checkout/confirm',        [CheckoutController::class, 'confirm'])->name('checkout.confirm');
    Route::get('/checkout/failed',          [CheckoutController::class, 'paymentFailed'])->name('checkout.failed');
    Route::get('/checkout/success/{id}',    [CheckoutController::class, 'success'])->name('checkout.success');
    // Legacy route alias (keeps backward compat)
    Route::post('/checkout',                [CheckoutController::class, 'createPaymentIntent'])->name('checkout.post');

    // Orders
    Route::get('/orders',                   [OrderController::class, 'index'])->name('orders');
    Route::get('/orders/{id}',              [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{id}/qr',           [OrderController::class, 'qrPage'])->name('orders.qr');
    Route::get('/orders/{id}/receipt',      [OrderController::class, 'downloadReceipt'])->name('orders.receipt');
    Route::post('/orders/{id}/cancel',      [OrderController::class, 'cancel'])->name('orders.cancel');

    // Loyalty
    Route::get('/loyalty',                  [LoyaltyController::class, 'index'])->name('loyalty');
    Route::post('/loyalty/redeem',          [LoyaltyController::class, 'redeem'])->name('loyalty.redeem');

    // Notifications
    Route::get('/notifications',            [CustomerNotification::class, 'index'])->name('notifications');
    Route::get('/notifications/unread-count',[CustomerNotification::class, 'unreadCount'])->name('notifications.unread-count');
    Route::post('/notifications/read-all',  [CustomerNotification::class, 'markAllRead'])->name('notifications.read-all');
    Route::post('/notifications/{id}/read', [CustomerNotification::class, 'markRead'])->name('notifications.read');

    // Profile
    Route::get('/profile',                  [CustomerProfile::class, 'index'])->name('profile');
    Route::put('/profile',                  [CustomerProfile::class, 'update'])->name('profile.update');
    Route::put('/profile/password',         [CustomerProfile::class, 'updatePassword'])->name('profile.password');
});

// ═══════════════════════════════════════════════════════════
//  BUSINESS OWNER ROUTES
// ═══════════════════════════════════════════════════════════
Route::prefix('business')->name('business.')->middleware(['auth', 'role:business_owner', 'business.approved'])->group(function () {
    Route::get('/dashboard',                [BusinessDashboard::class, 'index'])->name('dashboard');

    // Subscription & Payment
    Route::get('/subscription',             [SubscriptionController::class, 'index'])->name('subscription');
    Route::post('/subscription',            [SubscriptionController::class, 'subscribe'])->name('subscription.post');
    Route::get('/payment',                  [SubscriptionController::class, 'paymentPage'])->name('payment');

    // Food Management (requires active subscription)
    Route::middleware('subscription.active')->group(function () {
        Route::get('/food',                 [BusinessFoodController::class, 'index'])->name('food.index');
        Route::get('/food/add',             [BusinessFoodController::class, 'create'])->name('food.add');
        Route::get('/food/create',          [BusinessFoodController::class, 'create'])->name('food.create');
        Route::post('/food',                [BusinessFoodController::class, 'store'])->name('food.store');
        Route::get('/food/{id}/edit',       [BusinessFoodController::class, 'edit'])->name('food.edit');
        Route::put('/food/{id}',            [BusinessFoodController::class, 'update'])->name('food.update');
        Route::delete('/food/{id}',         [BusinessFoodController::class, 'destroy'])->name('food.delete');
        Route::post('/food/{id}/toggle-featured', [BusinessFoodController::class, 'toggleFeatured'])->name('food.featured');
    });

    // Reservations
    Route::get('/reservations',             [BusinessReservation::class, 'index'])->name('reservations');
    Route::get('/reservations/{id}',        [BusinessReservation::class, 'show'])->name('reservations.show');

    // QR Scanner
    Route::get('/scanner',                  [QRScannerController::class, 'index'])->name('scanner');
    Route::post('/scanner/verify',          [\App\Http\Controllers\API\QRVerifyController::class, 'verify'])->name('scanner.verify');

    // Earnings & Reports
    Route::get('/earnings',                 [EarningsController::class, 'index'])->name('earnings');
    Route::get('/earnings/export',          [EarningsController::class, 'export'])->name('earnings.export');
    Route::get('/commissions',              [EarningsController::class, 'commissions'])->name('commissions');

    // Analytics
    Route::get('/analytics',                [AnalyticsController::class, 'index'])->name('analytics');

    // Profile
    Route::get('/profile',                  [BusinessProfile::class, 'index'])->name('profile');
    Route::put('/profile',                  [BusinessProfile::class, 'update'])->name('profile.update');
});

// ═══════════════════════════════════════════════════════════
//  ADMIN ROUTES
// ═══════════════════════════════════════════════════════════
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard',                [AdminDashboard::class, 'index'])->name('dashboard');

    // Business Approvals
    Route::get('/businesses',               [BusinessApprovalController::class, 'index'])->name('businesses');
    Route::get('/businesses/{id}',          [BusinessApprovalController::class, 'show'])->name('businesses.show');
    Route::post('/businesses/{id}/approve', [BusinessApprovalController::class, 'approve'])->name('businesses.approve');
    Route::post('/businesses/{id}/reject',  [BusinessApprovalController::class, 'reject'])->name('businesses.reject');

    // Subscriptions
    Route::get('/subscriptions',            [SubscriptionManagementController::class, 'index'])->name('subscriptions');
    Route::post('/subscriptions/{id}/extend',[SubscriptionManagementController::class, 'extend'])->name('subscriptions.extend');
    Route::post('/subscriptions/{id}/cancel',[SubscriptionManagementController::class, 'cancel'])->name('subscriptions.cancel');

    // Users
    Route::get('/users',                    [UserManagementController::class, 'index'])->name('users');
    Route::post('/users/{id}/suspend',      [UserManagementController::class, 'suspend'])->name('users.suspend');
    Route::post('/users/{id}/activate',     [UserManagementController::class, 'activate'])->name('users.activate');
    Route::delete('/users/{id}',            [UserManagementController::class, 'destroy'])->name('users.delete');

    // Revenue
    Route::get('/revenue',                  [RevenueController::class, 'index'])->name('revenue');

    // Commissions
    Route::get('/commissions',              [CommissionController::class, 'index'])->name('commissions');
    Route::post('/commissions/settle',      [CommissionController::class, 'settle'])->name('commissions.settle');
    Route::put('/commissions/rate',         [CommissionController::class, 'updateRate'])->name('commissions.rate');

    // Reports
    Route::get('/reports',                  [ReportController::class, 'index'])->name('reports');
    Route::get('/reports/{type}/export',    [ReportController::class, 'export'])->name('reports.export');

    // Settings
    Route::get('/settings',                 [SettingsController::class, 'index'])->name('settings');
    Route::post('/settings',                [SettingsController::class, 'update'])->name('settings.update');

    // Payment Ledger
    Route::get('/payments',                 [App\Http\Controllers\Admin\PaymentLedgerController::class, 'index'])->name('payments.index');
    Route::get('/payments/export',          [App\Http\Controllers\Admin\PaymentLedgerController::class, 'export'])->name('payments.export');
});

// ═══════════════════════════════════════════════════════════
//  MAP & GEOLOCATION ROUTES
// ═══════════════════════════════════════════════════════════
Route::get('/map',                  [HomeController::class, 'map'])->name('map');
Route::get('/api/businesses/map',   [HomeController::class, 'businessesMapData'])->name('api.businesses.map');

// ═══════════════════════════════════════════════════════════
//  PAYMENT WEBHOOKS (No CSRF — must be excluded in VerifyCsrfToken)
// ═══════════════════════════════════════════════════════════
Route::post('/webhook/stripe', [App\Http\Controllers\API\PaymentWebhookController::class, 'handleStripe'])
    ->name('webhook.stripe');

// Legacy payment webhook alias
Route::post('/webhook/payment', [App\Http\Controllers\API\PaymentWebhookController::class, 'handle'])
    ->name('webhook.payment');
