<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\CustomerAuthController;
use App\Http\Controllers\OrderController;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//Route::get('/', [StoreController::class, 'index'])->name('store.index');

// Customer Auth API endpoints
Route::prefix('customer')->name('customer.')->group(function () {
    Route::post('/login', [CustomerAuthController::class, 'login'])->name('login');
    Route::post('/register', [CustomerAuthController::class, 'register'])->name('register');
    Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('logout');
    Route::post('/delete-account', [CustomerAuthController::class, 'deleteAccount'])->name('deleteAccount');
});
Route::post('/customer/forgot-password', [CustomerAuthController::class, 'sendResetLinkEmail'])->name('customer.password.email');
Route::post('/customer/reset-password', [CustomerAuthController::class, 'resetPassword'])->name('customer.password.update');
Route::post('/api/orders/place', [OrderController::class, 'placeOrder'])->name('orders.place');
Route::get('/api/customer/orders', [OrderController::class, 'customerOrders'])->name('orders.customer');
Route::post('/api/customer/orders/{id}/cancel', [OrderController::class, 'cancelOrder'])->name('orders.cancel');

// Storefront wildcard route for client-side routing
Route::get('/{any?}', [StoreController::class, 'index'])
    ->where('any', '^(?!backend|admin|customer|api|lang|email).*$')
    ->name('store');

// API routes handled by the frontend
Route::post('/api/auth/send-code', [StoreController::class, 'sendAuthCode']);
Route::post('/api/auth/verify-code', [StoreController::class, 'verifyAuthCode']);
Route::post('/api/orders/place', [StoreController::class, 'placeOrder']);

Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'ar'])) {
        Session::put('locale', $locale);
    }
    return back();
})->name('lang.switch');


Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
    // 1. Fetch the customer directly from the database
    $customer = DB::table('customers')->where('id', $id)->first();

    if (!$customer) {
        abort(404, 'Customer not found.');
    }

    // 2. Validate hash against customer's email
    if (!hash_equals((string) $hash, sha1($customer->email))) {
        abort(403, 'Invalid verification link.');
    }

    // 3. Update email_verified_at directly
    DB::table('customers')
        ->where('id', $id)
        ->update([
            'email_verified_at' => now(),
            'updated_at'        => now(),
        ]);

    return redirect('/profile/email-signin?verified=1');
})->name('verification.verify');

// Public Admin Auth Routes
Route::prefix('backend')->group(function () {
    // Other admin routes with admin. prefix
    Route::name('admin.')->group(function () {
        Route::get('/login', [AdminController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AdminController::class, 'login'])->name('login.submit');
        Route::post('/logout', [AdminController::class, 'logout'])->name('logout');

        Route::get('/forgot-password', [AdminController::class, 'showForgotPasswordForm'])->name('password.request');
        Route::post('/forgot-password', [AdminController::class, 'sendResetLinkEmail'])->name('password.email');
        Route::post('/reset-password', [AdminController::class, 'resetPassword'])->name('password.update');
    });

    // Name this specifically 'password.reset' so default Laravel mailer finds it
    Route::get('/reset-password/{token}', [AdminController::class, 'showResetPasswordForm'])->name('password.reset');
});

// Protected Admin Panel Routes (Requires Login)
Route::prefix('backend')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

    // Orders
    Route::get('/orders', [AdminController::class, 'orders'])->name('orders.index');
    Route::post('/orders/{order}/status', [AdminController::class, 'updateOrderStatus'])->name('orders.status');
    Route::post('/admin/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('admin.orders.status');

    // Products
    Route::get('/products', [AdminController::class, 'products'])->name('products.index');
    Route::get('/products/create', [AdminController::class, 'createProduct'])->name('products.create');
    Route::post('/products', [AdminController::class, 'storeProduct'])->name('products.store');
    Route::get('/products/{product}', [AdminController::class, 'showProduct'])->name('products.show');
    Route::get('/products/{product}/edit', [AdminController::class, 'editProduct'])->name('products.edit');
    Route::put('/products/{product}', [AdminController::class, 'updateProduct'])->name('products.update');
    Route::delete('/products/{product}', [AdminController::class, 'deleteProduct'])->name('products.delete');

    // Categories
    Route::get('/categories', [AdminController::class, 'categories'])->name('categories.index');
    Route::get('/categories/create', [AdminController::class, 'createCategory'])->name('categories.create');
    Route::post('/categories', [AdminController::class, 'storeCategory'])->name('categories.store');
    Route::get('/categories/{category}', [AdminController::class, 'showCategory'])->name('categories.show');
    Route::get('/categories/{category}/edit', [AdminController::class, 'editCategory'])->name('categories.edit');
    Route::put('/categories/{category}', [AdminController::class, 'updateCategory'])->name('categories.update');
    Route::delete('/categories/{category}', [AdminController::class, 'deleteCategory'])->name('categories.delete');
});
