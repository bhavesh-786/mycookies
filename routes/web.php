<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

use App\Http\Controllers\StoreController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CustomerAuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ==========================================
// 1. Customer Authentication & Account Endpoints
// ==========================================
Route::prefix('customer')->name('customer.')->group(function () {
    Route::post('/login', [CustomerAuthController::class, 'login'])->name('login');
    Route::post('/register', [CustomerAuthController::class, 'register'])->name('register');
    Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('logout');
    Route::post('/delete-account', [CustomerAuthController::class, 'deleteAccount'])->name('deleteAccount');
    Route::post('/forgot-password', [CustomerAuthController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::post('/reset-password', [CustomerAuthController::class, 'resetPassword'])->name('password.update');
});

// ==========================================
// 2. Customer Order & Storefront APIs
// ==========================================
Route::post('/api/orders/place', [OrderController::class, 'placeOrder'])->name('orders.place');
Route::get('/api/customer/orders', [OrderController::class, 'customerOrders'])->name('orders.customer');
Route::post('/api/customer/orders/{id}/cancel', [OrderController::class, 'cancelOrder'])->name('orders.cancel');

Route::post('/api/auth/send-code', [StoreController::class, 'sendAuthCode']);
Route::post('/api/auth/verify-code', [StoreController::class, 'verifyAuthCode']);

// ==========================================
// 3. Language & Verification Utilities
// ==========================================
Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'ar'])) {
        Session::put('locale', $locale);
    }
    return back();
})->name('lang.switch');

Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
    $customer = DB::table('customers')->where('id', $id)->first();

    if (!$customer) {
        abort(404, 'Customer not found.');
    }

    if (!hash_equals((string) $hash, sha1($customer->email))) {
        abort(403, 'Invalid verification link.');
    }

    DB::table('customers')
        ->where('id', $id)
        ->update([
            'email_verified_at' => now(),
            'updated_at'        => now(),
        ]);

    return redirect('/profile/email-signin?verified=1');
})->name('verification.verify');

// ==========================================
// 4. Public Admin Authentication Routes
// ==========================================
Route::prefix('backend')->name('admin.')->group(function () {
    Route::get('/login', [AdminController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AdminController::class, 'logout'])->name('logout');

    Route::get('/forgot-password', [AdminController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AdminController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::post('/reset-password', [AdminController::class, 'resetPassword'])->name('password.update');
    Route::get('/reset-password/{token}', [AdminController::class, 'showResetPasswordForm'])->name('password.reset');
});

// ==========================================
// 5. Protected Admin Routes (Role & Permission Guarded)
// ==========================================
Route::prefix('backend')->name('admin.')->middleware('auth')->group(function () {
    // Dashboard (Accessible to all logged-in admin staff)
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

    // --- Orders Management (Guarded by 'manage-orders') ---
    Route::middleware(['can:manage-orders'])->group(function () {
        Route::get('/orders', [AdminController::class, 'orders'])->name('orders.index');
        Route::post('/orders/{order}/status', [AdminController::class, 'updateOrderStatus'])->name('orders.status');
    });

    // --- Products Management (Guarded by 'manage-products') ---
    Route::middleware(['can:manage-products'])->group(function () {
        Route::get('/products', [AdminController::class, 'products'])->name('products.index');
        Route::get('/products/create', [AdminController::class, 'createProduct'])->name('products.create');
        Route::post('/products', [AdminController::class, 'storeProduct'])->name('products.store');
        Route::get('/products/{product}', [AdminController::class, 'showProduct'])->name('products.show');
        Route::get('/products/{product}/edit', [AdminController::class, 'editProduct'])->name('products.edit');
        Route::put('/products/{product}', [AdminController::class, 'updateProduct'])->name('products.update');
        Route::delete('/products/{product}', [AdminController::class, 'deleteProduct'])->name('products.delete');
        Route::post('/products/{product}/clone', [AdminController::class, 'productClone'])->name('products.clone');
    });

    // --- Categories Management (Guarded by 'manage-categories') ---
    Route::middleware(['can:manage-categories'])->group(function () {
        Route::get('/categories', [AdminController::class, 'categories'])->name('categories.index');
        Route::get('/categories/create', [AdminController::class, 'createCategory'])->name('categories.create');
        Route::post('/categories', [AdminController::class, 'storeCategory'])->name('categories.store');
        Route::get('/categories/{category}', [AdminController::class, 'showCategory'])->name('categories.show');
        Route::get('/categories/{category}/edit', [AdminController::class, 'editCategory'])->name('categories.edit');
        Route::put('/categories/{category}', [AdminController::class, 'updateCategory'])->name('categories.update');
        Route::delete('/categories/{category}', [AdminController::class, 'deleteCategory'])->name('categories.delete');
        Route::post('/categories/{category}/clone', [AdminController::class, 'categoriesClone'])->name('categories.clone');
    });

    // --- Users & Roles Management (Guarded by 'manage-users') ---
    Route::middleware(['can:manage-users'])->group(function () {
        // Users
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

        // Roles
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
        Route::get('/roles/{id}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('/roles/{id}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{id}', [RoleController::class, 'destroy'])->name('roles.destroy');
    });
});

// ==========================================
// 6. Storefront SPA Wildcard Route (MUST BE AT THE VERY BOTTOM)
// ==========================================
Route::get('/{any?}', [StoreController::class, 'index'])
    ->where('any', '^(?!backend|admin|customer|api|lang|email).*$')
    ->name('store');
