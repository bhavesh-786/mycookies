<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AddonGroup;
use App\Models\AddonOption;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    // 3. Products Management
    public function products(Request $request)
    {
        //$products = Product::with(['category', 'addonGroups.options'])->latest()->paginate(10);
        //return view('admin.products.index', compact('products'));

        $query = Product::with(['category', 'addonGroups.options']);

        $sortBy = $request->query('sort_by');
        $sortDir = strtolower($request->query('sort_dir')) === 'desc' ? 'desc' : 'asc';

        if ($sortBy === 'price') {
            $query->orderByRaw("base_price IS NULL, base_price {$sortDir}");
        } elseif ($sortBy === 'name') {
            $localeCol = app()->getLocale() === 'ar' ? 'name_ar' : 'name_en';
            $column = Schema::hasColumn('products', $localeCol) ? $localeCol : 'name';
            $query->orderBy($column, $sortDir);
        } else {
            // Default manual sort order first, then ID
            $query->orderBy('sort_order', 'asc')->orderBy('id', 'desc');
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
            'category_id'     => 'required|exists:categories,id',
            'name'            => 'required|string|max:255',
            'name_ar'         => 'required|string|max:255',
            'base_price'      => 'nullable|numeric',
            'description'     => 'nullable|string',
            'description_ar'  => 'nullable|string',
            'image_url'       => 'nullable|url',
            'image_file'      => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
        ]);

        $imagePath = $product->image;
        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('products', 'public');
            $imagePath = asset('storage/' . $path);
        } elseif ($request->filled('image_url')) {
            $imagePath = $request->input('image_url');
        }

        unset($validated['image_url'], $validated['image_file']);
        $validated['image'] = $imagePath;
        $validated['slug']  = Str::slug($request->name) . '-' . $product->id;

        DB::transaction(function () use ($request, $product, $validated) {
            $product->update($validated);

            // 1. Explicitly clear previous groups and child options
            $existingGroupIds = $product->addonGroups()->pluck('id');
            AddonOption::whereIn('addon_group_id', $existingGroupIds)->delete();
            $product->addonGroups()->delete();

            // 2. Re-create only submitted groups (if any)
            $groups = $request->input('groups', []);
            foreach ($groups as $grpData) {
                if (!empty($grpData['name'])) {
                    $group = AddonGroup::create([
                        'product_id'     => $product->id,
                        'name'           => $grpData['name'],
                        'name_ar'        => $grpData['name_ar'] ?? null,
                        'type'           => $grpData['type'] ?? 'radio',
                        'is_required'    => isset($grpData['is_required']) && $grpData['is_required'],
                        'min_selectable' => isset($grpData['min_selectable']) ? (int) $grpData['min_selectable'] : ($grpData['type'] === 'radio' ? 1 : 0),
                        'max_selectable' => !empty($grpData['max_selectable']) ? (int) $grpData['max_selectable'] : ($grpData['type'] === 'radio' ? 1 : 5),
                    ]);

                    if (!empty($grpData['options'])) {
                        foreach ($grpData['options'] as $optData) {
                            if (!empty($optData['name'])) {
                                AddonOption::create([
                                    'addon_group_id' => $group->id,
                                    'name'           => $optData['name'],
                                    'name_ar'        => $optData['name_ar'] ?? null,
                                    'price'          => $optData['price'] ?? 0.000,
                                ]);
                            }
                        }
                    }
                }
            }
        });

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully!');
    }

    public function productClone(Product $product)
    {
        DB::transaction(function () use ($product) {
            $replicatedProduct = $product->replicate(['slug']);
            $replicatedProduct->name = $product->name . ' (Copy)';
            if ($product->name_ar) {
                $replicatedProduct->name_ar = $product->name_ar . ' (نسخة)';
            }
            $replicatedProduct->slug = \Illuminate\Support\Str::slug($replicatedProduct->name) . '-' . \Illuminate\Support\Str::random(5);
            $replicatedProduct->save();

            // Deep copy addon groups and their individual options
            $product->load('addonGroups.options');
            foreach ($product->addonGroups as $group) {
                $replicatedGroup = $group->replicate();
                $replicatedGroup->product_id = $replicatedProduct->id;
                $replicatedGroup->save();

                foreach ($group->options as $option) {
                    $replicatedOption = $option->replicate();
                    $replicatedOption->addon_group_id = $replicatedGroup->id;
                    $replicatedOption->save();
                }
            }
        });

        return redirect()->route('admin.products.index')->with('success', __('Product cloned successfully with all addons.'));
    }

    public function deleteProduct(Product $product)
    {
        $product->delete();
        return back()->with('success', 'Product deleted.');
    }

    public function reorderProducts(Request $request)
    {
        $request->validate([
            'order'   => 'required|array',
            'order.*' => 'integer|exists:products,id',
        ]);

        DB::transaction(function () use ($request) {
            foreach ($request->order as $position => $id) {
                Product::where('id', $id)->update(['sort_order' => $position + 1]);
            }
        });

        return response()->json(['status' => 'success', 'message' => 'Product order saved']);
    }
}
