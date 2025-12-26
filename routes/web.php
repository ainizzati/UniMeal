<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\StudentAuthController;
use App\Http\Controllers\Auth\CafeteriaAuthController;
use App\Http\Controllers\MahallahController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;

/*
|--------------------------------------------------------------------------
| Root & Auth Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    if (Auth::guard('student')->check()) {
        return redirect()->route('student.home');
    } elseif (Auth::guard('cafeteria')->check()) {
        return redirect()->route('vendor.home');
    }
    return redirect()->route('login');
});

Route::get('/login', fn () => view('auth.login'))->name('login');
Route::post('/login', [StudentAuthController::class, 'login'])->name('login.post');
Route::post('/logout', function (Illuminate\Http\Request $request) {
    // Logout from all guards
    Auth::guard('student')->logout();
    Auth::guard('cafeteria')->logout();

    // Invalidate the session
    $request->session()->invalidate();

    // Regenerate CSRF token
    $request->session()->regenerateToken();

    return redirect()->route('login')->with('success', 'You have been logged out successfully.');
})->name('logout');

/*
|--------------------------------------------------------------------------
| Student Registration
|--------------------------------------------------------------------------
*/
Route::get('/register/student', [StudentAuthController::class, 'showRegisterForm'])
    ->name('student.register.form');
Route::post('/register/student', [StudentAuthController::class, 'register'])
    ->name('student.register.submit');

/*
|--------------------------------------------------------------------------
| Cafeteria (Vendor) Registration & Routes
|--------------------------------------------------------------------------
*/
Route::get('/register/cafeteria', [CafeteriaAuthController::class, 'showRegisterForm'])
    ->name('cafeteria.register.form');
Route::post('/register/cafeteria', [CafeteriaAuthController::class, 'register'])
    ->name('cafeteria.register.submit');

Route::middleware(['auth:cafeteria', 'prevent.back'])->prefix('vendor')->group(function () {
    Route::get('/home', [CafeteriaAuthController::class, 'home'])->name('vendor.home');
});

/*
|--------------------------------------------------------------------------
| Student Protected Routes
|--------------------------------------------------------------------------
*/
Route::get('/cafeteria/{mahallah}', [MahallahController::class, 'show'])->name('cafeteria.show');

Route::middleware(['auth:student', 'prevent.back'])->group(function () {
    // Dashboard
    Route::get('/student/home', fn () => view('student.home'))->name('student.home');

    // Mahallah Cafeteria Viewing
    Route::get('/cafeteria/{mahallah}', [MahallahController::class, 'show'])->name('cafeteria.show');

    // Cart
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::get('/cart', [CartController::class, 'show'])->name('cart.show');
    Route::put('/cart/update/{index}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{index}', [CartController::class, 'remove'])->name('cart.remove');

    // Checkout Flow
    Route::get('/checkout', [CheckoutController::class, 'form'])->name('checkout.form');
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/delivery', [CheckoutController::class, 'delivery'])->name('checkout.delivery');
    Route::post('/delivery', [CheckoutController::class, 'processDelivery'])->name('checkout.delivery.process');
    Route::get('/payment', [CheckoutController::class, 'payment'])->name('checkout.payment');
    Route::post('/payment', [CheckoutController::class, 'processPayment'])->name('checkout.payment.process');

    // Order Tracking
    Route::get('/orders', [OrderController::class, 'history'])->name('orders.history');
    Route::get('/orders/track/{id}', [OrderController::class, 'track'])->name('order.track');
    Route::get('/checkout/order-tracking', [CheckoutController::class, 'orderTracking'])->name('checkout.order_tracking');

    // Optional Success Page
    Route::get('/checkout/success', fn () => view('checkout.success'))->name('checkout.success');

    Route::get('/receipt/{order}', [CheckoutController::class, 'receipt'])->name('checkout.receipt');

});
