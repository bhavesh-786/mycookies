<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CategoriesController extends Controller
{
    // 4. Categories Management
    public function categories(Request $request)
    {
        $query = Category::withCount('products');

        // 1. Search Query Handling
        if ($request->filled('search')) {
            $search = trim($request->query('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('name_ar', 'LIKE', "%{$search}%")
                    ->orWhere('slug', 'LIKE', "%{$search}%");
            });
        }

        // 2. Sorting
        $sortBy = $request->query('sort_by');
        $sortDir = strtolower($request->query('sort_dir')) === 'desc' ? 'desc' : 'asc';

        if ($sortBy === 'name') {
            $localeCol = app()->getLocale() === 'ar' ? 'name_ar' : 'name';
            $column = Schema::hasColumn('categories', $localeCol) ? $localeCol : 'name';
            $query->orderBy($column, $sortDir);
        } else {
            $query->orderBy('sort_order', 'asc')->orderBy('id', 'desc');
        }

        $categories = $query->paginate(2);

        return view('admin.categories.index', compact('categories'));
    }

    public function reorderCategories(Request $request)
    {
        $request->validate([
            'order'   => 'required|array',
            'order.*' => 'integer|exists:categories,id',
        ]);

        DB::transaction(function () use ($request) {
            foreach ($request->order as $position => $id) {
                Category::where('id', $id)->update(['sort_order' => $position + 1]);
            }
        });

        return response()->json([
            'status'  => 'success',
            'message' => __('Category order saved successfully.'),
        ]);
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

        // Place new category at the end of the order
        $maxOrder = Category::max('sort_order') ?? 0;

        Category::create([
            'name' => $request->name,
            'name_ar' => $request->name_ar,
            'slug' => Str::slug($request->name),
            'image' => $imagePath,
            'sort_order' => $maxOrder + 1,
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

    public function editCategory(Request $request, Category $category)
    {
        $returnUrl = $request->query('return_url', route('admin.categories.index'));
        return view('admin.categories.edit', compact('category', 'returnUrl'));
    }

    public function updateCategory(Request $request, Category $category)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'name_ar'     => 'required|string|max:255',
            'image_url'   => 'nullable|url',
            'image_file'  => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
            'return_url'  => 'nullable|string',
        ]);

        $imagePath = $category->image;
        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('categories', 'public');
            $imagePath = asset('storage/' . $path);
        } elseif ($request->filled('image_url')) {
            $imagePath = $request->input('image_url');
        }

        $returnUrl = $request->input('return_url', route('admin.categories.index'));

        $category->update([
            'name'    => $request->name,
            'name_ar' => $request->name_ar,
            'slug'    => Str::slug($request->name),
            'image'   => $imagePath,
        ]);

        return redirect($returnUrl)->with('success', 'Category updated successfully!');
    }

    public function deleteCategory(Category $category)
    {
        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully!');
    }

    public function categoriesClone(Category $category)
    {
        $replicatedCategory = $category->replicate(['slug']);
        $replicatedCategory->name = $category->name . ' (Copy)';
        if ($category->name_ar) {
            $replicatedCategory->name_ar = $category->name_ar . ' (نسخة)';
        }
        $replicatedCategory->slug = Str::slug($replicatedCategory->name) . '-' . Str::random(5);
        $replicatedCategory->sort_order = (Category::max('sort_order') ?? 0) + 1;
        $replicatedCategory->save();

        return redirect()->route('admin.categories.index')->with('success', __('Category cloned successfully.'));
    }
}
