<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\IntroController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PaymentMethodController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductImageController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SocialController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VariantController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function (): void {
    // Authentication
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.submit');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    // Locale
    Route::get('locale/{locale}', [AuthController::class, 'switchLocale'])->name('locale');

    // Authenticated admin routes
    Route::middleware('admin')->group(function (): void {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Settings
        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('settings', [SettingController::class, 'update'])->name('settings.update');

        // Notifications
        Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::get('notifications/create', [NotificationController::class, 'create'])->name('notifications.create');
        Route::post('notifications', [NotificationController::class, 'store'])->name('notifications.store');
        Route::delete('notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

        // Users
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('users', [UserController::class, 'store'])->name('users.store');
        Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::post('users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');
        Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        // Admins
        Route::get('admins', [AdminController::class, 'index'])->name('admins.index');
        Route::get('admins/create', [AdminController::class, 'create'])->name('admins.create');
        Route::post('admins', [AdminController::class, 'store'])->name('admins.store');
        Route::get('admins/{user}/edit', [AdminController::class, 'edit'])->name('admins.edit');
        Route::put('admins/{user}', [AdminController::class, 'update'])->name('admins.update');
        Route::delete('admins/{user}', [AdminController::class, 'destroy'])->name('admins.destroy');

        // Categories
        Route::resource('categories', CategoryController::class)->except('show');

        // Products
        Route::resource('products', ProductController::class);
        Route::delete('products/images/{image}', [ProductImageController::class, 'destroy'])->name('products.images.destroy');
        Route::put('products/images/{image}/primary', [ProductImageController::class, 'setPrimary'])->name('products.images.primary');

        // Variants
        Route::resource('variants', VariantController::class)->except('show');

        // Orders
        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::put('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');

        // Coupons
        Route::resource('coupons', CouponController::class)->except('show');

        // Contact Messages
        Route::get('contact-messages', [ContactMessageController::class, 'index'])->name('contact-messages.index');
        Route::get('contact-messages/{contactMessage}', [ContactMessageController::class, 'show'])->name('contact-messages.show');
        Route::put('contact-messages/{contactMessage}/read', [ContactMessageController::class, 'markAsRead'])->name('contact-messages.mark-read');
        Route::delete('contact-messages/{contactMessage}', [ContactMessageController::class, 'destroy'])->name('contact-messages.destroy');

        // Intros
        Route::resource('intros', IntroController::class)->except('show');

        // Banners
        Route::resource('banners', BannerController::class)->except('show');

        // Payment Methods
        Route::resource('payment-methods', PaymentMethodController::class)->except('show')->names('payment_methods');

        // Socials
        Route::get('socials', [SocialController::class, 'index'])->name('socials.index');
        Route::get('socials/create', [SocialController::class, 'create'])->name('socials.create');
        Route::post('socials', [SocialController::class, 'store'])->name('socials.store');
        Route::get('socials/{social}/edit', [SocialController::class, 'edit'])->name('socials.edit');
        Route::put('socials/{social}', [SocialController::class, 'update'])->name('socials.update');
        Route::delete('socials/{social}', [SocialController::class, 'destroy'])->name('socials.destroy');
    });
});
