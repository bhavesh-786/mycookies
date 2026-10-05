<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

use App\Http\Controllers\StoreController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CustomerAuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AreaController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoriesController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\GovernorateController;
use App\Http\Controllers\Admin\PickStoreController;
use App\Http\Controllers\Admin\SettingController;

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
    Route::post('/send-verification', [CustomerAuthController::class, 'sendVerification'])->name('sendVerification');
    Route::get('/verify-email/{id}', [CustomerAuthController::class, 'verifyEmail'])->name('verify.email');
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
    // Dashboard (All logged-in staff)
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

    // --- Orders Management ---
    Route::middleware(['can:manage-orders'])->group(function () {
        Route::get('/orders', [AdminController::class, 'orders'])->name('orders.index');
        Route::post('/orders/{order}/status', [AdminController::class, 'updateOrderStatus'])->name('orders.status');
    });

    // --- Products Management ---
    Route::middleware(['can:manage-products'])->group(function () {
        Route::get('/products', [ProductController::class, 'products'])->name('products.index');
        Route::get('/products/create', [ProductController::class, 'createProduct'])->name('products.create');
        Route::post('/products', [ProductController::class, 'storeProduct'])->name('products.store');
        Route::get('/products/{product}', [ProductController::class, 'showProduct'])->name('products.show');
        Route::get('/products/{product}/edit', [ProductController::class, 'editProduct'])->name('products.edit');
        Route::put('/products/{product}', [ProductController::class, 'updateProduct'])->name('products.update');
        Route::delete('/products/{product}', [ProductController::class, 'deleteProduct'])->name('products.delete');
        Route::post('/products/{product}/clone', [ProductController::class, 'productClone'])->name('products.clone');
        Route::post('/products/reorder', [ProductController::class, 'reorderProducts'])->name('products.reorder');
    });

    // --- Categories Management ---
    Route::middleware(['can:manage-categories'])->group(function () {
        Route::get('/categories', [CategoriesController::class, 'categories'])->name('categories.index');
        Route::get('/categories/create', [CategoriesController::class, 'createCategory'])->name('categories.create');
        Route::post('/categories', [CategoriesController::class, 'storeCategory'])->name('categories.store');
        Route::get('/categories/{category}', [CategoriesController::class, 'showCategory'])->name('categories.show');
        Route::get('/categories/{category}/edit', [CategoriesController::class, 'editCategory'])->name('categories.edit');
        Route::put('/categories/{category}', [CategoriesController::class, 'updateCategory'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoriesController::class, 'deleteCategory'])->name('categories.delete');
        Route::post('/categories/{category}/clone', [CategoriesController::class, 'categoriesClone'])->name('categories.clone');
        Route::post('/categories/reorder', [CategoriesController::class, 'reorderCategories'])->name('categories.reorder');
    });

    // --- Customers Management ---
    Route::middleware(['can:manage-customers'])->group(function () {
        Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
        Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');
    });

    // --- Governorates Management ---
    Route::middleware(['can:manage-governorates'])->group(function () {
        Route::resource('governorates', GovernorateController::class);
    });

    // --- Delivery Areas Management ---
    Route::middleware(['can:manage-areas'])->group(function () {
        Route::resource('areas', AreaController::class);
    });

    // --- Pickup Stores Management ---
    Route::middleware(['can:manage-stores'])->group(function () {
        Route::resource('pickstores', PickStoreController::class)->parameters([
            'pickstores' => 'store'
        ]);
    });

    // --- Users & Roles Management ---
    Route::middleware(['can:manage-users'])->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('roles', RoleController::class);
    });

    // --- Settings Management ---
    Route::middleware(['can:manage-settings'])->group(function () {
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'updateSettings'])->name('settings.update');
    });
});

// ==========================================
// 6. Storefront SPA Wildcard Route (MUST BE AT THE VERY BOTTOM)
// ==========================================
Route::get('/{any?}', [StoreController::class, 'index'])
    ->where('any', '^(?!backend|admin|customer|api|lang|email).*$')
    ->name('store');
