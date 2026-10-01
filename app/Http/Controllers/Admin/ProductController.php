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
    public function products(Request $request)
    {
        $query = Product::with(['category', 'addonGroups.options']);

        // 0. Category Filtering
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->query('category_id'));
        }

        // 1. Search Query Handling
        if ($request->filled('search')) {
            $search = trim($request->query('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('name_ar', 'LIKE', "%{$search}%")
                    ->orWhereHas('category', function ($catQuery) use ($search) {
                        $catQuery->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('name_ar', 'LIKE', "%{$search}%");
                    });
            });
        }

        // 2. Sorting
        $sortBy = $request->query('sort_by');
        $sortDir = strtolower($request->query('sort_dir')) === 'desc' ? 'desc' : 'asc';

        if ($sortBy === 'price') {
            $query->orderByRaw("base_price IS NULL, base_price {$sortDir}");
        } elseif ($sortBy === 'name') {
            $localeCol = app()->getLocale() === 'ar' ? 'name_ar' : 'name_en';
            $column = Schema::hasColumn('products', $localeCol) ? $localeCol : 'name';
            $query->orderBy($column, $sortDir);
        } else {
            $query->orderBy('sort_order', 'asc')->orderBy('id', 'desc');
        }

        $products = $query->paginate(15);

        // Optional: Fetch category details if filtering by one to show a header badge
        $selectedCategory = $request->filled('category_id')
            ? Category::find($request->query('category_id'))
            : null;

        return view('admin.products.index', compact('products', 'selectedCategory'));
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

    public function editProduct(Request $request, Product $product)
    {
        $product->load('addonGroups.options');
        $categories = Category::all();

        // Capture return URL to redirect back after update
        $returnUrl = $request->query('return_url', route('admin.products.index'));

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

        return view('admin.products.edit', compact('product', 'categories', 'initialGroups', 'returnUrl'));
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
            'return_url'      => 'nullable|string', // To preserve current page
        ]);

        $imagePath = $product->image;
        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('products', 'public');
            $imagePath = asset('storage/' . $path);
        } elseif ($request->filled('image_url')) {
            $imagePath = $request->input('image_url');
        }

        $returnUrl = $request->input('return_url', route('admin.products.index'));

        unset($validated['image_url'], $validated['image_file'], $validated['return_url']);
        $validated['image'] = $imagePath;
        $validated['slug']  = Str::slug($request->name) . '-' . $product->id;

        DB::transaction(function () use ($request, $product, $validated) {
            $product->update($validated);

            $existingGroupIds = $product->addonGroups()->pluck('id');
            AddonOption::whereIn('addon_group_id', $existingGroupIds)->delete();
            $product->addonGroups()->delete();

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

        return redirect($returnUrl)->with('success', 'Product updated successfully!');
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
