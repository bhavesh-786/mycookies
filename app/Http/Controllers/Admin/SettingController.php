<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');
        return view('admin.settings.index', compact('settings'));
    }

    public function edit($id)
    {
        $settings = Setting::findOrFail($id);

        return view('admin.roles.edit', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'store_status' => 'required|string|max:255',
            'min_order' => 'required|numeric|min:0',
            'default_delivery_fee' => 'nullable|numeric|min:0',
            'instagram_handle' => 'nullable|string|max:255',
            'operating_hours' => 'required|array',
            'operating_hours.*.days' => 'required|string|max:255',
            'operating_hours.*.open' => 'nullable|string',
            'operating_hours.*.close' => 'nullable|string',
            'operating_hours.*.closed' => 'nullable',
        ]);

        // Normalize operating hours to ensure 'closed' is explicitly boolean (true/false)
        if (isset($validated['operating_hours'])) {
            $validated['operating_hours'] = array_map(function ($row) {
                $row['closed'] = isset($row['closed']) && filter_var($row['closed'], FILTER_VALIDATE_BOOLEAN);
                return $row;
            }, $validated['operating_hours']);
        }

        // Save standard fields & encode operating hours array as JSON
        foreach ($validated as $key => $value) {
            if ($key === 'operating_hours') {
                $value = json_encode($value);
            }
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return redirect()->back()->with('success', __('Settings saved successfully!'));
    }
}
