<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Governorate;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    public function index()
    {
        $areas = Area::with('governorate')->latest()->paginate(20);
        return view('admin.areas.index', compact('areas'));
    }

    public function create()
    {
        $governorates = Governorate::orderBy('name_en')->get();
        return view('admin.areas.create', compact('governorates'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'governorate_id' => 'required|exists:governorates,id',
            'name_en' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'delivery_fee' => 'required|numeric|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        Area::create($validated);

        return redirect()->route('admin.areas.index')->with('success', 'Area created successfully.');
    }

    public function edit(Area $area)
    {
        $governorates = Governorate::orderBy('name_en')->get();
        return view('admin.areas.create', compact('area', 'governorates'));
    }

    public function update(Request $request, Area $area)
    {
        $validated = $request->validate([
            'governorate_id' => 'required|exists:governorates,id',
            'name_en' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'delivery_fee' => 'required|numeric|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        $area->update($validated);

        return redirect()->route('admin.areas.index')->with('success', 'Area updated successfully.');
    }

    public function destroy(Area $area)
    {
        $area->delete();
        return redirect()->route('admin.areas.index')->with('success', 'Area deleted successfully.');
    }
}
