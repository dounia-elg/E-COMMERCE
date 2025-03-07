<?php

use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminUserController;

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// Product Routes
Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('products.index');
    Route::get('/{product:slug}', [ProductController::class, 'show'])->name('products.show');
    Route::get('/category/{category:slug}', [ProductController::class, 'byCategory'])->name('products.category');
    Route::get('/search', [ProductController::class, 'search'])->name('products.search');
});

// Cart Routes
Route::prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('cart.index');
    Route::post('/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/update', [CartController::class, 'update'])->name('cart.update');
    Route::post('/remove/{cartItem}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/clear', [CartController::class, 'clear'])->name('cart.clear');
});

// Newsletter subscription
Route::post('/newsletter/subscribe', [HomeController::class, 'subscribeNewsletter'])->name('newsletter.subscribe');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Email Verification Routes
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [EmailVerificationController::class, 'showVerificationNotice'])
        ->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'resend'])
        ->middleware(['throttle:6,1'])
        ->name('verification.resend');
});

// Authenticated User Routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Profile Routes
    Route::prefix('profile')->group(function () {
        Route::get('/', [UserProfileController::class, 'index'])->name('profile.index');
        Route::put('/', [UserProfileController::class, 'update'])->name('profile.update');
        Route::get('/orders', [UserProfileController::class, 'orders'])->name('profile.orders');
        Route::get('/orders/{order}', [UserProfileController::class, 'showOrder'])->name('profile.orders.show');
        Route::get('/addresses', [UserProfileController::class, 'addresses'])->name('profile.addresses');
        Route::get('/reviews', [UserProfileController::class, 'reviews'])->name('profile.reviews');
        Route::get('/wishlist', [UserProfileController::class, 'wishlist'])->name('profile.wishlist');
        Route::get('/settings', [UserProfileController::class, 'settings'])->name('profile.settings');
        Route::put('/settings/password', [UserProfileController::class, 'updatePassword'])->name('profile.update-password');
    });
    
    // Checkout Routes
    Route::prefix('checkout')->group(function () {
        Route::get('/', [CheckoutController::class, 'index'])->name('checkout.index');
        Route::post('/process', [CheckoutController::class, 'process'])->name('checkout.process');
        Route::get('/complete/{order}', [CheckoutController::class, 'complete'])->name('checkout.complete');
    });
    
    // Wishlist Routes
    Route::prefix('wishlist')->group(function () {
        Route::post('/add/{product}', [WishlistController::class, 'add'])->name('wishlist.add');
        Route::delete('/remove/{product}', [WishlistController::class, 'remove'])->name('wishlist.remove');
    });
    
    // Review Routes
    Route::post('/reviews/{product}', [ReviewController::class, 'store'])->name('reviews.store');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
});

// Admin Routes
Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Admin Product Routes
    Route::resource('products', AdminProductController::class);
    
    // Admin Category Routes
    Route::resource('categories', AdminCategoryController::class);
    
    // Admin Order Routes
    Route::resource('orders', AdminOrderController::class);
    Route::post('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');
    
    // Admin User Routes
    Route::resource('users', AdminUserController::class);
});