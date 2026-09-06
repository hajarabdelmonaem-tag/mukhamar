<?php

use App\Http\Controllers\Api\V1\AddressController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CartController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\ContactController;
use App\Http\Controllers\Api\V1\HomeController;
use App\Http\Controllers\Api\V1\IntroController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\PaymentMethodController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\WishlistController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    // Public
    Route::get('intros', [IntroController::class, 'index']);
    Route::get('home', [HomeController::class, 'index']);
    Route::get('categories', [CategoryController::class, 'index']);
    Route::get('categories/{category:slug}/products', [CategoryController::class, 'products']);

    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/featured', [ProductController::class, 'featured']);
    Route::get('products/{product:slug}', [ProductController::class, 'show']);

    // Auth
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/login', [AuthController::class, 'login']);
    Route::post('auth/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('auth/verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('auth/reset-password', [AuthController::class, 'resetPassword']);
    Route::post('auth/verify-phone', [AuthController::class, 'verifyPhone']);

    // Contact (public)
    Route::post('contact', [ContactController::class, 'store']);

    Route::middleware('auth:sanctum')->group(function (): void {
        // Auth
        Route::post('auth/logout', [AuthController::class, 'logout']);

        // Profile
        Route::get('profile', [ProfileController::class, 'show']);
        Route::put('profile', [ProfileController::class, 'update']);
        Route::put('profile/password', [ProfileController::class, 'updatePassword']);
        Route::delete('profile', [ProfileController::class, 'destroy']);

        // Categories & Products
        Route::post('products/{product:slug}/reviews', [ProductController::class, 'storeReview']);

        // Wishlist
        Route::get('wishlist', [WishlistController::class, 'index']);
        Route::post('wishlist', [WishlistController::class, 'store']);
        Route::delete('wishlist/{product:slug}', [WishlistController::class, 'destroy']);

        // Cart
        Route::get('cart', [CartController::class, 'show']);
        Route::post('cart/items', [CartController::class, 'addItem']);
        Route::put('cart/items/{item}', [CartController::class, 'updateItem']);
        Route::delete('cart/items/{item}', [CartController::class, 'removeItem']);
        Route::post('cart/coupon', [CartController::class, 'applyCoupon']);
        Route::delete('cart/coupon', [CartController::class, 'removeCoupon']);

        // Addresses
        Route::get('addresses', [AddressController::class, 'index']);
        Route::post('addresses', [AddressController::class, 'store']);
        Route::put('addresses/{address}', [AddressController::class, 'update']);
        Route::delete('addresses/{address}', [AddressController::class, 'destroy']);
        Route::post('addresses/{address}/default', [AddressController::class, 'makeDefault']);

        // Orders
        Route::get('orders', [OrderController::class, 'index']);
        Route::post('orders', [OrderController::class, 'store']);
        Route::get('orders/{order}', [OrderController::class, 'show']);
        Route::get('orders/{order}/invoice', [OrderController::class, 'invoice']);

        // Payment methods (supported methods only)
        Route::get('payment-methods', [PaymentMethodController::class, 'index']);

        // Notifications
        Route::get('notifications', [NotificationController::class, 'index']);
        Route::post('notifications/{notification}/read', [NotificationController::class, 'markAsRead']);
        Route::post('notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    });
});
