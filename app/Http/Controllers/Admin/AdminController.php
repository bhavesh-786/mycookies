<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\AddonGroup;
use App\Models\AddonOption;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminController extends Controller
{

    // 0. Authentication Methods
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }


    // ==========================================
    // PASSWORD RESET WORKFLOW
    // ==========================================

    public function showForgotPasswordForm()
    {
        return view('admin.auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetPasswordForm(Request $request, $token)
    {
        return view('admin.auth.reset-password', [
            'token' => $token,
            'email' => $request->email
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('admin.login')->with('success', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    // 1. Dashboard Overview
    public function dashboard()
    {
        $ordersCount = Order::count();
        $productsCount = Product::count();
        $categoriesCount = Category::count();
        $recentOrders = Order::latest()->take(5)->get();

        return view('admin.dashboard', compact('ordersCount', 'productsCount', 'categoriesCount', 'recentOrders'));
    }

    // 2. Orders Management
    public function orders()
    {
        $orders = Order::with('items')->latest()->paginate(10);
        return view('admin.orders.index', compact('orders'));
    }

    public function updateOrderStatus(Request $request, Order $order)
    {
        // $request->validate(['order_status' => 'required']);
        // $order->update(['order_status' => $request->order_status]);
        // return back()->with('success', 'Order status updated successfully!');

        $request->validate([
            'order_status' => 'required|string',
        ]);

        $newStatus = trim($request->input('order_status'));

        // Update whichever column exists directly via DB Query Builder to bypass any model restrictions
        $updateData = [];
        if (Schema::hasColumn('orders', 'order_status')) {
            $updateData['order_status'] = $newStatus;
        }
        if (Schema::hasColumn('orders', 'status')) {
            $updateData['status'] = $newStatus;
        }

        DB::table('orders')->where('id', $order->id)->update($updateData);

        return back()->with('success', __('Order status updated successfully.'));
    }

    // 3. Products Management
    public function products(Request $request)
    {
        //$products = Product::with(['category', 'addonGroups.options'])->latest()->paginate(10);
        //return view('admin.products.index', compact('products'));

        $query = Product::with(['category', 'addonGroups.options']);

        $sortBy = $request->query('sort_by');
        $sortDir = strtolower($request->query('sort_dir')) === 'desc' ? 'desc' : 'asc';

        if ($sortBy === 'price') {
            // Sorts by base_price (places null values at the end)
            $query->orderByRaw("base_price IS NULL, base_price {$sortDir}");
        } elseif ($sortBy === 'name') {
            $localeCol = app()->getLocale() === 'ar' ? 'name_ar' : 'name_en';
            $column = Schema::hasColumn('products', $localeCol) ? $localeCol : 'name';
            $query->orderBy($column, $sortDir);
        } else {
            $query->latest();
        }

        $products = $query->paginate(15);

        return view('admin.products.index', compact('products'));
    }

    public function createProduct()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function storeProduct(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'base_price' => 'nullable|numeric',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'image_url' => 'nullable|url',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
        ]);

        // 1. Determine image source
        $imagePath = $request->input('image_url');
        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('products', 'public');
            $imagePath = asset('storage/' . $path);
        }

        // 2. Remove the form-only fields so they do not hit the SQL query
        unset($validated['image_url'], $validated['image_file']);

        // 3. Assign to the actual database column 'image'
        $validated['image'] = $imagePath;
        $validated['slug'] = Str::slug($request->name) . '-' . rand(100, 999);

        $product = Product::create($validated);

        // Save Addon Groups if provided
        if ($request->has('groups')) {
            foreach ($request->groups as $grpData) {
                if (!empty($grpData['name'])) {
                    $group = AddonGroup::create([
                        'product_id' => $product->id,
                        'name' => $grpData['name'],
                        'name_ar' => $grpData['name_ar'] ?? null,
                        'type' => $grpData['type'] ?? 'radio',
                        'is_required' => isset($grpData['is_required']),
                        'min_selectable' => isset($grpData['min_selectable']) ? (int) $grpData['min_selectable'] : ($grpData['type'] === 'radio' ? 1 : 0),
                        'max_selectable' => isset($grpData['max_selectable']) && !empty($grpData['max_selectable']) ? (int) $grpData['max_selectable'] : ($grpData['type'] === 'radio' ? 1 : 5),
                    ]);

                    if (!empty($grpData['options'])) {
                        foreach ($grpData['options'] as $optData) {
                            if (!empty($optData['name'])) {
                                AddonOption::create([
                                    'addon_group_id' => $group->id,
                                    'name' => $optData['name'],
                                    'name_ar' => $optData['name_ar'] ?? null,
                                    'price' => $optData['price'] ?? 0.000,
                                ]);
                            }
                        }
                    }
                }
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully!');
    }

    // ==========================================
    // PRODUCT CRUD (VIEW & EDIT)
    // ==========================================
    public function showProduct(Product $product)
    {
        $product->load(['category', 'addonGroups.options']);
        return view('admin.products.show', compact('product'));
    }

    public function editProduct(Product $product)
    {
        $product->load('addonGroups.options');
        $categories = Category::all();

        // Prepare JSON data directly in PHP
        $initialGroups = $product->addonGroups->map(function ($g) {
            return [
                'name' => $g->name,
                'name_ar' => $g->name_ar ?? '',
                'type' => $g->type,
                'is_required' => (bool) $g->is_required,
                'options' => $g->options->map(function ($o) {
                    return [
                        'name' => $o->name,
                        'name_ar' => $o->name_ar ?? '',
                        'price' => (float) $o->price,
                    ];
                })->values(),
            ];
        })->values();

        return view('admin.products.edit', compact('product', 'categories', 'initialGroups'));
    }

    public function updateProduct(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'base_price' => 'nullable|numeric',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'image_url' => 'nullable|url',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
        ]);

        // 1. Determine image source
        $imagePath = $product->image;
        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('products', 'public');
            $imagePath = asset('storage/' . $path);
        } elseif ($request->filled('image_url')) {
            $imagePath = $request->input('image_url');
        }

        // 2. Remove the form-only fields
        unset($validated['image_url'], $validated['image_file']);

        // 3. Assign to the actual database column 'image'
        $validated['image'] = $imagePath;
        $validated['slug'] = Str::slug($request->name) . '-' . $product->id;

        $product->update($validated);

        // Sync addon groups
        if ($request->has('groups')) {
            $product->addonGroups()->delete();

            foreach ($request->groups as $grpData) {
                if (!empty($grpData['name'])) {
                    $group = AddonGroup::create([
                        'product_id' => $product->id,
                        'name' => $grpData['name'],
                        'name_ar' => $grpData['name_ar'] ?? null,
                        'type' => $grpData['type'] ?? 'radio',
                        'is_required' => isset($grpData['is_required']),
                        'min_selectable' => isset($grpData['min_selectable']) ? (int) $grpData['min_selectable'] : ($grpData['type'] === 'radio' ? 1 : 0),
                        'max_selectable' => isset($grpData['max_selectable']) && !empty($grpData['max_selectable']) ? (int) $grpData['max_selectable'] : ($grpData['type'] === 'radio' ? 1 : 5),
                    ]);

                    if (!empty($grpData['options'])) {
                        foreach ($grpData['options'] as $optData) {
                            if (!empty($optData['name'])) {
                                AddonOption::create([
                                    'addon_group_id' => $group->id,
                                    'name' => $optData['name'],
                                    'name_ar' => $optData['name_ar'] ?? null,
                                    'price' => $optData['price'] ?? 0.000,
                                ]);
                            }
                        }
                    }
                }
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully!');
    }

    public function deleteProduct(Product $product)
    {
        $product->delete();
        return back()->with('success', 'Product deleted.');
    }

    // 4. Categories Management
    public function categories(Request $request)
    {
        $query = Category::withCount('products');

        $sortBy = $request->query('sort_by');
        $sortDir = strtolower($request->query('sort_dir')) === 'desc' ? 'desc' : 'asc';

        if ($sortBy === 'name') {
            $localeCol = app()->getLocale() === 'ar' ? 'name_ar' : 'name';
            $column = \Illuminate\Support\Facades\Schema::hasColumn('categories', $localeCol) ? $localeCol : 'name';
            $query->orderBy($column, $sortDir);
        } else {
            $query->latest();
        }

        $categories = $query->paginate(10);

        return view('admin.categories.index', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'image_url' => 'nullable|url',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
        ]);

        $imagePath = $request->input('image_url');

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('categories', 'public');
            $imagePath = asset('storage/' . $path);
        }

        Category::create([
            'name' => $request->name,
            'name_ar' => $request->name_ar,
            'slug' => Str::slug($request->name),
            'image' => $imagePath,
        ]);

        return back()->with('success', 'Category added successfully.');
    }

    public function createCategory()
    {
        return view('admin.categories.create');
    }

    public function showCategory(Category $category)
    {
        $category->load('products.addonGroups.options');
        return view('admin.categories.show', compact('category'));
    }

    public function editCategory(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function updateCategory(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'image_url' => 'nullable|url',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
        ]);

        $imagePath = $category->image;

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('categories', 'public');
            $imagePath = asset('storage/' . $path);
        } elseif ($request->filled('image_url')) {
            $imagePath = $request->input('image_url');
        }

        $category->update([
            'name' => $request->name,
            'name_ar' => $request->name_ar,
            'slug' => Str::slug($request->name),
            'image' => $imagePath,
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully!');
    }

    public function deleteCategory(Category $category)
    {
        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully!');
    }
}
