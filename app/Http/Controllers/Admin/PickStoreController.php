<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;

class PickStoreController extends Controller
{
    public function index()
    {
        $stores = Store::latest()->paginate(15);
        return view('admin.stores.index', compact('stores'));
    }

    public function create()
    {
        return view('admin.stores.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location_description' => 'nullable|string|max:500',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        Store::create($validated);

        return redirect()->route('admin.pickstores.index')->with('success', 'Store branch added successfully.');
    }

    public function edit(Store $store)
    {
        return view('admin.stores.create', compact('store'));
    }

    public function update(Request $request, Store $store)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location_description' => 'nullable|string|max:500',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        $store->update($validated);

        return redirect()->route('admin.pickstores.index')->with('success', 'Store branch updated successfully.');
    }

    public function destroy(Store $store)
    {
        $store->delete();
        return redirect()->route('admin.pickstores.index')->with('success', 'Store branch deleted successfully.');
    }
}
