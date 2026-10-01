<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Governorate;
use Illuminate\Http\Request;

class GovernorateController extends Controller
{
    public function index()
    {
        $governorates = Governorate::withCount('areas')->latest()->paginate(15);
        return view('admin.governorates.index', compact('governorates'));
    }

    public function create()
    {
        return view('admin.governorates.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
        ]);

        Governorate::create($validated);

        return redirect()->route('admin.governorates.index')->with('success', 'Governorate created successfully.');
    }

    public function edit(Governorate $governorate)
    {
        return view('admin.governorates.create', compact('governorate'));
    }

    public function update(Request $request, Governorate $governorate)
    {
        $validated = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
        ]);

        $governorate->update($validated);

        return redirect()->route('admin.governorates.index')->with('success', 'Governorate updated successfully.');
    }

    public function destroy(Governorate $governorate)
    {
        $governorate->delete();
        return redirect()->route('admin.governorates.index')->with('success', 'Governorate deleted successfully.');
    }
}
