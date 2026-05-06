<?php

declare(strict_types=1);

use App\Http\Controllers\AddressController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderAdminController;
use App\Http\Controllers\Admin\ProductAdminController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserAdminController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Jobs\SendWelcomeAfterVerificationJob;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

Route::view('/', 'main')->name('home');

Route::middleware('guest')->group(function () {
    // registration
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register.form');
    Route::post('/register', [AuthController::class, 'register'])->name('register');

    // login
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (
        EmailVerificationRequest $request,
    ) {
        $user = $request->user();
        $wasVerified = $user->hasVerifiedEmail();

        $request->fulfill();

        if (!$wasVerified) {
            SendWelcomeAfterVerificationJob::dispatch($user->id);
        }

        return redirect()
            ->route('profile.form')
            ->with('success', 'Email успешно подтверждён!');
    })->middleware('signed')->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();

        return back()->with('success', 'Ссылка для подтверждения отправлена на вашу почту.');
    })->middleware('throttle:6,1')->name('verification.send');

    Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile.form');
    Route::patch('/profile/{id}', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::get('/change-password', [AuthController::class, 'showChangePasswordForm'])->name('password.form');
    Route::post('/change-password', [AuthController::class, 'updatePassword'])->name('password.update');

    Route::post('/profile/addresses', [AddressController::class, 'store'])->name('addresses.store');
    Route::patch('/profile/addresses/{address}', [AddressController::class, 'update'])->name('addresses.update');
    Route::delete('/profile/addresses/{address}', [AddressController::class, 'destroy'])->name('addresses.destroy');
    Route::patch('/profile/addresses/{address}/default', [AddressController::class, 'setDefault'])
        ->name('addresses.default');

    Route::middleware('verified')->group(function () {
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
        Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])
            ->name('orders.status.update');
    });
});

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/items/{product}', [CartController::class, 'store'])->name('cart.items.store');
Route::patch('/cart/items/{product}', [CartController::class, 'update'])->name('cart.items.update');
Route::delete('/cart/items/{product}', [CartController::class, 'destroy'])->name('cart.items.destroy');
Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear');

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('roles', RoleController::class)->except(['show']);
        Route::resource('users', UserAdminController::class);
        Route::patch('users/{user}/password', [UserAdminController::class, 'resetPassword'])
            ->name('users.password');
        Route::resource('products', ProductAdminController::class);
        Route::resource('orders', OrderAdminController::class);
    });
